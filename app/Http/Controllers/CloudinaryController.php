<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CloudinaryController extends Controller
{
    /**
     *
     * Nota: storeOnCloudinaryAs existe en la versión moderna del paquete y permite indicar el nombre del archivo. Si no
     * quieres el mismo nombre, puedes generar un nombre único con uniqid() o Str::slug().
     *
     *En Laravel 12 con la versión actual del paquete, la forma correcta de subir a Cloudinary es usando el disco Storage, no los métodos en UploadedFile.

     * composer require cloudinary-labs/cloudinary-laravel
     * php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider" --tag="cloudinary-laravel-config"
     */
    public function subirImagenCloud(Request $request){

        $messages = [
        'image.required' => 'Falta el archivo',
        'image.mimes' => 'Tipo no soportado',
        'image.max' => 'El archivo excede el tamaño máximo permitido',
        ];

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

       if ($request->hasFile('image') && $request->file('image')->isValid()) {
        try {
            $file = $request->file('image');

            // Generamos un nombre único para la imagen
            // Obtenemos nombre y extensión por separado
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            // Generamos nombre único y seguro
            $filename = uniqid('img_') . '_' . Str::slug($originalName) . '.' . $extension;

            // Subimos el archivo usando el disco "cloudinary"
            $uploadedFilePath = Storage::disk('cloudinary')->putFileAs('laravel', $file, $filename);

            // Obtenemos la URL pública
            $url = Storage::disk('cloudinary')->url($uploadedFilePath);

            return response()->json(['url' => $url], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al subir la imagen: ' . $e->getMessage()], 500);
        }
        }

        return response()->json(['error' => 'No se recibió ningún archivo.'], 400);
    }


    public function descargarImagenCloud($publicId)
    {
       // Construimos la ruta en el disco Cloudinary
        $filePath = 'laravel/' . $publicId;

        try {
            // Obtenemos la URL pública de Cloudinary
            $url = Storage::disk('cloudinary')->url($filePath);

            if (empty($url)) {
                return response()->json(['error' => 'No existe la imagen'], 404);
            }

            return response()->json(['url' => $url], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la imagen: ' . $e->getMessage()], 500);
    }
    }
}
