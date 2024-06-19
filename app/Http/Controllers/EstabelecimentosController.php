<?php

namespace App\Http\Controllers;

use App\Models\Estabelecimento;
use Illuminate\Http\Request;
use App\Exceptions\MazeException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\MazeHelper;

class EstabelecimentosController extends Controller
{
    public function index()
    {
        try
        {
            $Estabelecimento = Estabelecimento::all();

            return response()->json($Estabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar os Estabelecimentos', 500);
        }
    }

    public function show($id)
    {
		try {
            if(!$Estabelecimento = Estabelecimento::find($id))
            {
                throw new MazeException('Estabelecimento não encontrado.', 404);
            }

            return response()->json($Estabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar o Estabelecimento', 500);
        }
    }

    public function store(Request $request)
    {
        try
        {   
			
            $Estabelecimento = new Estabelecimento;
            $Estabelecimento->fill($request->all());
            $Estabelecimento->save();

            return response()->json($Estabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar o Estabelecimento', 500);
        }
    }

    public function update(Request $request, $id)
    {
		try {
			$validator = Validator::make($request->all(), Estabelecimento::$rules_update, MazeHelper::get_mensagens_validacao());

			if($validator->fails())
			{
				throw new MazeException($validator->errors()->first(), 400);
			}
			
            if(!$Estabelecimento = Estabelecimento::find($id))
            {
                throw new MazeException('Estabelecimento não encontrado.', 404);
            }

            $Estabelecimento->fill($request->all());
            $Estabelecimento->save();

            return response()->json($Estabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar o Estabelecimento', 500);
        }
    }

    public function delete($id)
    {
        try
        {
            if(!$retorno = Estabelecimento::find($id))
            {
                throw new MazeException('Estabelecimento não encontrado.', 404);
            }

            $Estabelecimento = Estabelecimento::destroy($id);

            return response()->json($Estabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar o Estabelecimento', 500);
        }
    }
}
