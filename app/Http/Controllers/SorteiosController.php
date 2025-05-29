<?php

namespace App\Http\Controllers;

use App\Models\Sorteio;
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

class SorteiosController extends Controller
{

    public function index()
    {
        try
        {
            $Sorteio = Sorteio::all();

            return response()->json($Sorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar os Sorteios', 500);
        }
    }

    public function show($id)
    {
		try {
            if(!$Sorteio = Sorteio::find($id))
            {
                throw new MazeException('Sorteio não encontrado.', 404);
            }

            return response()->json($Sorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar o Sorteio', 500);
        }
    }

    public function showApp()
    {
		try {

            $user = JWTAuth::user();
    
            if(!$Sorteio = Sorteio::where('sorteios.data_fim', ">" ,Carbon::now())
            ->join("estabelecimentos", "estabelecimentos.id", "sorteios.estabelecimentos_id")
            ->select("sorteios.*","estabelecimentos.nome as nome_estabelecimento")
            ->first())
            {
                throw new MazeException('Sorteio não encontrado.', 404);
            }

            $participa_sorteio = UsersSorteio::where("users_id",$user->id)
            ->where("sorteios_id", $Sorteio->id)->first();

            $dados = new stdClass;
            $dados->sorteio = $Sorteio;
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
            throw new MazeException('Não foi possível listar o Sorteio', 500);
        }
    }

    
    public function store(Request $request)
    {
        try
        {   
			
            $Sorteio = new Sorteio;
            $Sorteio->fill($request->all());

            if($arquivo = $request->file('foto')) {
                $url = 'https://s3.' . config('app.AWS_DEFAULT_REGION') . '.amazonaws.com/' . config('app.AWS_BUCKET') . '/';
                $name = time() . '_' . $this->clean(strtolower($request['nome']))   . '.' . $arquivo->getClientOriginalExtension();
                
                $filePath = 'arquivos/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($arquivo));
                $Sorteio->foto = $url.$filePath;
            }


            $Sorteio->save();

            return response()->json($Sorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar o Sorteio', 500);
        }
    }

    public function update(Request $request, $id)
    {
		try {
            if(!$Sorteio = Sorteio::find($id))
            {
                throw new MazeException('Sorteio não encontrado.', 404);
            }

            // Atualizar os campos normais
            $Sorteio->fill($request->except('foto'));

            // Tratar a foto
            if($arquivo = $request->file('foto')) {
                $url = 'https://s3.' . config('app.AWS_DEFAULT_REGION') . '.amazonaws.com/' . config('app.AWS_BUCKET') . '/';
                $name = time() . '_' . $this->clean(strtolower($request['nome']))   . '.' . $arquivo->getClientOriginalExtension();
                
                $filePath = 'arquivos/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($arquivo));
                $Sorteio->foto = $url.$filePath;
            }

            $Sorteio->save();

            return response()->json($Sorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar o Sorteio', 500);
        }
    }

    public function delete($id)
    {
        try
        {
            if(!$retorno = Sorteio::find($id))
            {
                throw new MazeException('Sorteio não encontrado.', 404);
            }

            $Sorteio = Sorteio::destroy($id);

            return response()->json($Sorteio, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar o Sorteio', 500);
        }
    }

    private function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
