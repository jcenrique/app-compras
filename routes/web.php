<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientPasswordResetController;

// Route::get('/', function () {
//     return view('welcome');
// });

// filepath: routes/web.php


Route::get('/test-mail', function () {
    Mail::raw('Este es un correo de prueba.', function ($message) {
        $message->to('jcenrique170@gmail.com')
                ->subject('Prueba de correo');
    });
    return 'Correo enviado';
});

