<?php

namespace App\Http\Controllers;

use App\Models\AtracoesEstabelecimento;
use App\Models\User;
use App\Models\Sorteio;
use Illuminate\Http\Request;
use App\Exceptions\MazeException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\MazeHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AtracoesEstabelecimentosController extends Controller
{

    public function index()
    {
        try
        {
            $AtracoesEstabelecimento = AtracoesEstabelecimento::all();

            return response()->json($AtracoesEstabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar os AtracoesEstabelecimentos', 500);
        }
    }

    public function show($id)
    {
		try {
            if(!$AtracoesEstabelecimento = AtracoesEstabelecimento::find($id))
            {
                throw new MazeException('AtracoesEstabelecimento não encontrado.', 404);
            }

            return response()->json($AtracoesEstabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar o AtracoesEstabelecimento', 500);
        }
    }

    public function showApp()
    {
		try {
            if(!$atracao = DB::table('atracoes_estabelecimento')
            ->whereRaw('atracoes_estabelecimento.data_atracao >= FROM_UNIXTIME(UNIX_TIMESTAMP(SUBDATE(NOW(),INTERVAL 1 WEEK)))')
            ->join("estabelecimentos", "estabelecimentos.id", "atracoes_estabelecimento.estabelecimentos_id")
            ->select("atracoes_estabelecimento.*","estabelecimentos.nome as nome_estabelecimento",
             "estabelecimentos.foto as foto_estabelecimento", "estabelecimentos.mapa")
            ->get())
            {
                throw new MazeException('Sorteio não encontrado.', 404);
            }

            return response()->json($atracao, 200);
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
			
            $AtracoesEstabelecimento = new AtracoesEstabelecimento;
            $AtracoesEstabelecimento->fill($request->all());

            if($arquivo = $request->file('foto')) {
                $url = 'https://s3.' . config('app.AWS_DEFAULT_REGION') . '.amazonaws.com/' . config('app.AWS_BUCKET') . '/';
                $name = time() . '_' . $this->clean(strtolower($request['nome']))   . '.' . $arquivo->getClientOriginalExtension();
                
                $filePath = 'arquivos/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($arquivo));
                $AtracoesEstabelecimento->foto = $url.$filePath;
            }


            $AtracoesEstabelecimento->save();

            return response()->json($AtracoesEstabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar o AtracoesEstabelecimento', 500);
        }
    }

    public function update(Request $request, $id)
    {
		try {
            if(!$AtracoesEstabelecimento = AtracoesEstabelecimento::find($id))
            {
                throw new MazeException('AtracoesEstabelecimento não encontrado.', 404);
            }

            // Atualizar os campos normais
            $AtracoesEstabelecimento->fill($request->except('foto'));

            // Tratar a foto
            if($arquivo = $request->file('foto')) {
                $url = 'https://s3.' . config('app.AWS_DEFAULT_REGION') . '.amazonaws.com/' . config('app.AWS_BUCKET') . '/';
                $name = time() . '_' . $this->clean(strtolower($request['nome']))   . '.' . $arquivo->getClientOriginalExtension();
                
                $filePath = 'arquivos/' . $name;
                Storage::disk('s3')->put($filePath, file_get_contents($arquivo));
                $AtracoesEstabelecimento->foto = $url.$filePath;
            }

            $AtracoesEstabelecimento->save();

            return response()->json($AtracoesEstabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar o AtracoesEstabelecimento', 500);
        }
    }

    public function delete($id)
    {
        try
        {
            if(!$retorno = AtracoesEstabelecimento::find($id))
            {
                throw new MazeException('AtracoesEstabelecimento não encontrado.', 404);
            }

            $AtracoesEstabelecimento = AtracoesEstabelecimento::destroy($id);

            return response()->json($AtracoesEstabelecimento, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar o AtracoesEstabelecimento', 500);
        }
    }

    private function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
