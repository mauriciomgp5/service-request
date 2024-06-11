<?php

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::fallback(function ($page) {
    Notification::make()
        ->title('Página não encontrada')
        ->body("A página {$page} não foi encontrada.")
        ->danger()
        ->send();

    return redirect('/');
});
