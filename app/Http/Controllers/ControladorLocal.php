<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


/**
 * php artisan storage:link
 * Esto hace que la carpeta storage/app/public sea pública.
 */
class ControladorLocal extends Controller
{

    public function subirImagenLocal(Request $request){
        $messages = [
            'max' => 'El campo se excede del tamaño máximo',
            'required' => 'Falta el archivo',
            'mimes' => 'Tipo no soportado'
        ];

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        if ($validator->fails()){
            return response()->json($validator->errors(),202);
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $filename = uniqid('img_') . $file->getClientOriginalName();
            $path = $file->storeAs('perfiles', $filename, 'public');
            //$url = Storage::disk('local')->url($path);
            //Generamos la URL completa accesible por el cliente. Con esta url la imagen es accesible.
            $url = asset("storage/perfiles/$filename");



            return response()->json(['path' => $path, 'url' => $url], 200);
        }
        return response()->json(['error' => 'No se recibió ningún archivo.'], 400);

    }

    public function mostrarImagen($filename)
    {
        //Ruta del archivo en el sistema de almacenamiento local.
        $path = storage_path('app/public/perfiles/' . $filename. '.jpg');

        //Verificamos si el archivo existe.
        if (!file_exists($path)) {
            return response()->json(['error' => 'Imagen no encontrada'], 404);
        }

        //Retornar la imagen al cliente.
        return response()->file($path);
    }

    public function descargarImagen($filename)
    {
        $path = storage_path('app/public/perfiles/' . $filename. '.jpg');

        if (!file_exists($path)) {
            return response()->json(['error' => 'Imagen no encontrada'], 404);
        }

        //Descargamos la imagen.
        return response()->download($path);
    }
}
