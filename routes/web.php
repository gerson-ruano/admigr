<?php

use App\Http\Controllers\FacturaController;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    //return view('welcome');
    return redirect('/escritorio');
});

Route::get('/factura/{dispatch}', [FacturaController::class, 'show'])
    ->name('factura.dispatch');