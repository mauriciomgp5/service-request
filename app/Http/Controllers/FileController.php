<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function show($file)
    {
        // Verifica a autorização do usuário
        $path = storage_path("app/service-requests/{$file}");

        if (!auth()->check()) {
            abort(403);
        }

        return response()->file($path);
    }

}
