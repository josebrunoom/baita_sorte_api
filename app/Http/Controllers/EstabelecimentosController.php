<?php

namespace App\Http\Controllers;

use App\Models\Estabelecimento;
use App\Models\User;
use App\Models\Sorteio;
use Illuminate\Http\Request;
use App\Exceptions\MazeException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\MazeHelper;
use Illuminate\Support\Facades\Storage;

class EstabelecimentosController extends Controller
{
    public function dashboard()
    {
        try
        {

            $response = array();
            $Estabelecimento = Estabelecimento::all();

            $response['estabelecimentos'] = Estabelecimento::count();
            $response['users'] = User::count();
            $response['sorteios'] = Sorteio::count();

            return response()->json($response, 200);
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

            if($arquivo = $request->file('foto')) {
                $url = 'https://s3.' . config('app.AWS_DEFAULT_REGION') . '.amazonaws.com/' . config('app.AWS_BUCKET') . '/';
                $name = time() . '_' . $this->clean(strtolower($request['nome']))   . '.' . $arquivo->getClientOriginalExtension();
                
                $filePath = 'arquivos/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($arquivo));
                $Estabelecimento->foto = $url.$filePath;
            }


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
			
			
            if(!$Estabelecimento = Estabelecimento::find($id))
            {
                throw new MazeException('Estabelecimento não encontrado.', 404);
            }

            $Estabelecimento->fill($request->all());

            if ($imagem = $request->file('foto')) {
                $url = 'https://s3.' . config('app.AWS_DEFAULT_REGION') . '.amazonaws.com/' . config('app.AWS_BUCKET') . '/';
                $name = time() . '_' . $this->clean(strtolower($Estabelecimento->nome));
                $filePath = 'arquivos/' . $name;

                $path_delete = str_replace($url,'',$Estabelecimento->foto);
                $a = Storage::disk('s3')->delete($path_delete);

                Storage::disk('s3')->put($filePath, file_get_contents($imagem));
                $Estabelecimento->foto = $url . $filePath;
            }

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

    private function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
