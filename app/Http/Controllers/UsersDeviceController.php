<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersDevice;
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

class UsersDeviceController extends Controller
{

    public function index()
    {
        try
        {
            $UsersDevice = UsersDevice::all();

            return response()->json($UsersDevice, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar os UsersDevice', 500);
        }
    }

    public function show($id)
    {
		try {
            if(!$UsersDevice = UsersDevice::find($id))
            {
                throw new MazeException('UsersDevice não encontrado.', 404);
            }

            return response()->json($UsersDevice, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar o UsersDevice', 500);
        }
    }

    public function showApp()
    {
		try {

            $user = JWTAuth::user();
    
            if(!$UsersDevice = UsersDevice::where('sorteios.data_fim', ">" ,Carbon::now())
            ->join("estabelecimentos", "estabelecimentos.id", "sorteios.estabelecimentos_id")
            ->select("sorteios.*","estabelecimentos.nome as nome_estabelecimento")
            ->first())
            {
                throw new MazeException('UsersDevice não encontrado.', 404);
            }

            $participa_sorteio = UsersUsersDevice::where("users_id",$user->id)
            ->where("sorteios_id", $UsersDevice->id)->first();

            $dados = new stdClass;
            $dados->sorteio = $UsersDevice;
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
            throw new MazeException('Não foi possível listar o UsersDevice', 500);
        }
    }

    
    public function store(Request $request)
    {
        try
        {   

            if(!$user = JWTAuth::user()){
                throw new MazeException('Usuario não encontrado.', 404);
            }
			
            $UsersDevice = new UsersDevice;
            $UsersDevice->fill($request->all());
            
            $UsersDevice->users_id = $user->id;

            $UsersDevice->save();

            return response()->json($UsersDevice, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar o UsersDevice', 500);
        }
    }

    public function update(Request $request, $id)
    {
		try {
			
			
            if(!$UsersDevice = UsersDevice::find($id))
            {
                throw new MazeException('UsersDevice não encontrado.', 404);
            }

            $UsersDevice->fill($request->all());
            $UsersDevice->save();

            return response()->json($UsersDevice, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar o UsersDevice', 500);
        }
    }

    public function delete($id)
    {
        try
        {
            if(!$retorno = UsersDevice::find($id))
            {
                throw new MazeException('UsersDevice não encontrado.', 404);
            }

            $UsersDevice = UsersDevice::destroy($id);

            return response()->json($UsersDevice, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar o UsersDevice', 500);
        }
    }

    private function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
