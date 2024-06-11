<!-- resources/views/errors/403.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">

</head>

<body class="bg-gray-600 dark:bg-gray-900 h-screen flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 max-w-md mx-auto">
        <h1 class="text-3xl font-bold text-red-500 dark:text-red-400 mb-4">403 Forbidden</h1>
        <p class="text-gray-700 dark:text-gray-300 mb-6">Você não tem permissão para acessar esta página.</p>
        <a href="{{ url('/') }}"
            class="bg-blue-500 dark:bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-600 dark:hover:bg-blue-800 transition duration-300">Voltar
            para Home</a>
    </div>
</body>

</html>
