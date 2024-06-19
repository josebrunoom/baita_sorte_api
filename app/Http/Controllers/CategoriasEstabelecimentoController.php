<?php

namespace App\Http\Controllers;

use App\Models\CategoriasEstabelecimento;
use Illuminate\Http\Request;
use App\Exceptions\MazeException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\MazeHelper;

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
            if(!$marca = CategoriasEstabelecimento::find($id))
            {
                throw new MazeException('Categoria não encontrada.', 404);
            }

            return response()->json($marca, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar a marca', 500);
        }
    }

    public function store(Request $request)
    {
        try
        {   
			
            $Categoria = new CategoriasEstabelecimento;
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
            throw new MazeException('Não foi possível cadastrar a marca', 500);
        }
    }

    public function update(Request $request, $id)
    {
		try {
			$validator = Validator::make($request->all(), CategoriasEstabelecimento::$rules_update, MazeHelper::get_mensagens_validacao());

			if($validator->fails())
			{
				throw new MazeException($validator->errors()->first(), 400);
			}
			
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
            throw new MazeException('Não foi possível atualizar a marca', 500);
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
}
