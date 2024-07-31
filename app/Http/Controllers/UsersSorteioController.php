<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersSorteio;
use \stdClass;
use Illuminate\Http\Request;
use App\Exceptions\MazeException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\MazeHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Crypt;

class UsersSorteioController extends Controller
{

    public function index()
    {
        try
        {
            $UsersSorteio = UsersSorteio::all();

            return response()->json($UsersSorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar os UsersSorteio', 500);
        }
    }

    public function show($id)
    {
		try {
            if(!$UsersSorteio = UsersSorteio::find($id))
            {
                throw new MazeException('UsersSorteio não encontrado.', 404);
            }

            return response()->json($UsersSorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar o UsersSorteio', 500);
        }
    }

    public function showApp()
    {
		try {

            $user = JWTAuth::user();
    
            if(!$UsersSorteio = UsersSorteio::where('sorteios.data_fim', ">" ,Carbon::now())
            ->join("estabelecimentos", "estabelecimentos.id", "sorteios.estabelecimentos_id")
            ->select("sorteios.*","estabelecimentos.nome as nome_estabelecimento")
            ->first())
            {
                throw new MazeException('UsersSorteio não encontrado.', 404);
            }

            $participa_sorteio = UsersUsersSorteio::where("users_id",$user->id)
            ->where("sorteios_id", $UsersSorteio->id)->first();

            $dados = new stdClass;
            $dados->sorteio = $UsersSorteio;
            $dados->participa = $participa_sorteio;

            return response()->json($dados, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar o UsersSorteio', 500);
        }
    }

    
    public function store(Request $request)
    {
        try
        {   

            if(!$user = JWTAuth::user()){
                throw new MazeException('Usuario não encontrado.', 404);
            }
			
            $UsersSorteio = new UsersSorteio;
            $UsersSorteio->fill($request->all());
            
            $UsersSorteio->users_id = $user->id;
            $UsersSorteio->pesquisa_estabelecimento_id = 1;
            

            $UsersSorteio->save();

            return response()->json($UsersSorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar o UsersSorteio', 500);
        }
    }

    public function update(Request $request, $id)
    {
		try {
			
			
            if(!$UsersSorteio = UsersSorteio::find($id))
            {
                throw new MazeException('UsersSorteio não encontrado.', 404);
            }

            $UsersSorteio->fill($request->all());
            $UsersSorteio->save();

            return response()->json($UsersSorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar o UsersSorteio', 500);
        }
    }

    public function delete($id)
    {
        try
        {
            if(!$retorno = UsersSorteio::find($id))
            {
                throw new MazeException('UsersSorteio não encontrado.', 404);
            }

            $UsersSorteio = UsersSorteio::destroy($id);

            return response()->json($UsersSorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar o UsersSorteio', 500);
        }
    }

    private function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
