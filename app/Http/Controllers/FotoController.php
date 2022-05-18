<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FotoController extends Controller
{
    protected function replaceOriginalImage(Media $media)
    {
        $fotoOriginal = public_path('storage\\'.$media->id.'\\'.$media->file_name);
        $fotoOtimizada = public_path('storage\\'.$media->id.'\\conversions\\'.$media->name.'-foto.jpg');

       if (copy($fotoOtimizada, $fotoOriginal) && unlink($fotoOtimizada))
       {
           $media->size = filesize(public_path('storage\\'.$media->id.'\\'.$media->file_name));
           $media->save();

           return true;
       }
       return false;
    }


    public function getFotos($ponto_id, $pescado_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->findOrFail($pescado_id);
        }
        catch (ModelNotFoundException)
        {
           return response()->json(['message' => 'Requisição inválida'], 400);
        }

        return $pescado->getMedia()->all();

    }

    public function getFoto($ponto_id, $pescado_id, $media_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->findOrFail($pescado_id);
            $media = $pescado->getMedia()->find($media_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 400);
        }

        if (is_null($media))
        {
            return response()->json(['message' => 'Requisição inválida'], 400);
        }

        return $media;
    }

    public function novaFoto(Request $request)
    {
        /*
        * Validação dos campos
         */
        $request->validate([
            'ponto_id' => 'required|integer',
            'pescado_id' => 'required|integer',
            'foto' => 'required|mimes:jpeg,png|max:100000'
        ]);

        try {
            $ponto = auth()->user()->pontos()->findOrFail($request->ponto_id);
            $pescado = $ponto->pescados()->findOrFail($request->pescado_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 400);
        }

        $nomeFoto = time();

        $pescado->addMediaFromRequest('foto')->usingName($nomeFoto)->usingFileName($nomeFoto.'.jpg')->toMediaCollection();

        $novaFoto = $pescado->media()->latest()->first();

        if ($this->replaceOriginalImage($novaFoto))
        {
            return response()->json(['message' => 'Foto cadastrada com sucesso'], 201);
        }

        return response()->json(['message' => 'Erro no processamento da foto'], 403);
    }

    //falta concluir - talvez essa função não seja necessária
    public function updateFoto($ponto_id, $pescado_id, $foto_id, Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->findOrFail($pescado_id);
            $foto = $pescado->fotos()->findOrFail($foto_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 406);
        }
        $foto->update($request->all());

        return response()->json($foto, 200);
    }

    public function deleteFoto($ponto_id, $pescado_id, $media_id)
    {
        try {
            $ponto = auth()->user()->pontos()->findOrFail($ponto_id);
            $pescado = $ponto->pescados()->findOrFail($pescado_id);
            $media = $pescado->getMedia()->find($media_id);
        }
        catch (ModelNotFoundException)
        {
            return response()->json(['message' => 'Requisição inválida'], 406);
        }

        if (is_null($media))
        {
            return response()->json(['message' => 'Requisição inválida'], 400);
        }

        $media->delete();

        return response()->json(['message' => 'A foto foi apagada com sucesso'], 200);
    }
}
