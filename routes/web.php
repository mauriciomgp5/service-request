<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use Filament\Notifications\Notification;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/storage/service-requests/{file}', [FileController::class, 'show'])->name('secure.view');


Route::fallback(function ($page) {
    Notification::make()
        ->title('Página não encontrada')
        ->body("A página {$page} não foi encontrada.")
        ->danger()
        ->send();

    return redirect('/');
});
