<?php

namespace App\Http\Controllers;

use App\Models\CategoriasEstabelecimento;
use Illuminate\Http\Request;
use App\Exceptions\MazeException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\MazeHelper;
use Illuminate\Support\Facades\Storage;

class CategoriasEstabelecimentoController extends Controller
{
    public function index()
    {
        try
        {
            $Categoria = CategoriasEstabelecimento::all();

            return response()->json($Categoria, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar as Categorias', 500);
        }
    }

    public function show($id)
    {
		try {
            if(!$categoria = CategoriasEstabelecimento::find($id))
            {
                throw new MazeException('Categoria não encontrada.', 404);
            }

            return response()->json($categoria, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar a categoria', 500);
        }
    }

    public function store(Request $request)
    {
        try
        {   
			
            $Categoria = new CategoriasEstabelecimento;
            $Categoria->fill($request->all());

            if($arquivo = $request->file('icone')) {
                $url = 'https://s3.' . config('app.AWS_DEFAULT_REGION') . '.amazonaws.com/' . config('app.AWS_BUCKET') . '/';
                $name = time() . '_' . $this->clean(strtolower($request['nome']))   . '.' . $arquivo->getClientOriginalExtension();
                
                $filePath = 'arquivos/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($arquivo));
                $Categoria->icone = $url.$filePath;
            }

            $Categoria->save();

            return response()->json($Categoria, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar a categoria', 500);
        }
    }

    public function update(Request $request, $id)
    {
		try {
			
			
            if(!$Categoria = CategoriasEstabelecimento::find($id))
            {
                throw new MazeException('Categoria não encontrada.', 404);
            }

            $Categoria->fill($request->all());
            $Categoria->save();

            return response()->json($Categoria, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar a categoria', 500);
        }
    }

    public function delete($id)
    {
        try
        {
            if(!$retorno = CategoriasEstabelecimento::find($id))
            {
                throw new MazeException('Categoria não encontrada.', 404);
            }

            $Categoria = CategoriasEstabelecimento::destroy($id);

            return response()->json($Categoria, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar a Categoria', 500);
        }
    }

    private function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
