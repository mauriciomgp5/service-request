#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Carrega variáveis do .env
if [ -f .env ]; then
    export $(cat .env | grep -v '#' | awk '/^[A-Z]/ {print}')
else
    echo -e "${RED}Arquivo .env não encontrado${NC}"
    exit 1
fi

# Verifica se as variáveis necessárias existem
if [ -z "$DEPLOY_HOST" ] || [ -z "$DEPLOY_USER" ] || [ -z "$DEPLOY_PORT" ] || [ -z "$DEPLOY_KEY_PATH" ]; then
    echo -e "${RED}Variáveis de deploy não encontradas no .env${NC}"
    echo "Adicione as seguintes variáveis ao seu .env:"
    echo "DEPLOY_HOST=seu_ip"
    echo "DEPLOY_USER=seu_usuario"
    echo "DEPLOY_PORT=sua_porta"
    echo "DEPLOY_KEY_PATH=caminho_da_chave"
    exit 1
fi

# Verifica se o branch atual é dev
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$CURRENT_BRANCH" != "dev" ]; then
    echo -e "${RED}Push não está no branch dev. Pulando deploy.${NC}"
    exit 0
fi

echo -e "${GREEN}Iniciando deploy remoto para $DEPLOY_HOST...${NC}"

# Comando SSH base
SSH_CMD="ssh -i $DEPLOY_KEY_PATH -p $DEPLOY_PORT $DEPLOY_USER@$DEPLOY_HOST"

# Lista de comandos para executar remotamente
REMOTE_COMMANDS="cd /home/dockers/nr-app && \
    git pull && \
    docker exec service-requests composer install --no-dev --optimize-autoloader && \
    # docker exec service-requests php artisan down && \
    docker exec service-requests php artisan migrate --force && \
    docker exec service-requests php artisan config:cache && \
    docker exec service-requests php artisan route:cache && \
    docker exec service-requests php artisan view:cache && \
    docker exec service-requests php artisan optimize && \
    docker exec service-requests php artisan cache:clear && \
    # docker exec service-requests php artisan up"

# Executa comandos remotamente
echo -e "${GREEN}Executando comandos no servidor...${NC}"
$SSH_CMD "$REMOTE_COMMANDS"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Deploy remoto completado com sucesso!${NC}"
else
    echo -e "${RED}Erro durante o deploy remoto${NC}"
    exit 1
fi