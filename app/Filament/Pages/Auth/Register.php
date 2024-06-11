<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Events\Auth\Registered;
use App\Notifications\UserRegistered;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Notification;
use Filament\Pages\Auth\Register as AuthRegister;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Notifications\Notification as NotificationsNotification;

class Register extends AuthRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data')
            ),
        ];
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);

        return $data;
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->mask('(99) 99999-9999')
            ->placeholder('(99) 99999-9999')
            ->label('Celular')
            ->unique($this->getUserModel(), 'phone', modifyRuleUsing: function (Unique $rule, $state) {
                return $rule->where('phone', preg_replace('/[^0-9]/', '', $state));
            })
            ->required()
            ->autofocus();
    }

    protected function beforeValidate(): void
    {
        $this->data['phone'] = preg_replace('/[^0-9]/', '', $this->data['phone']);
    }

    protected function afterValidate(): void
    {
        //mask phone
        $this->data['phone'] = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $this->data['phone']);
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        User::where('is_admin', true)->where('is_active', true)->get()->each(fn ($admin) => $admin->notify(new UserRegistered($user)));

        NotificationsNotification::make()
            ->title('Cadastro efetuado!')
            ->body('Seu cadastro foi efetuado com sucesso. Aguarde a liberação do seu acesso.')
            ->success()
            ->duration(7000)
            ->send();

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }
}
