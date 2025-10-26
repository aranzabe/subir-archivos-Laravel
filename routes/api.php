<?php

use App\Http\Controllers\CloudinaryController;
use App\Http\Controllers\ControladorLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/subirlocal', [ControladorLocal::class,'subirImagenLocal']);
Route::get('/mostrar/{filename}', [ControladorLocal::class, 'mostrarImagen']);
Route::get('/descargar/{filename}', [ControladorLocal::class, 'descargarImagen']);

Route::post('/subircloud', [CloudinaryController::class,'subirImagenCloud']);
Route::get('/mostrarcloud/{filename}', [CloudinaryController::class, 'descargarImagenCloud']);

// Route::post('/subirs3',[ControladorS3::class,'cargarImagenS3']);
