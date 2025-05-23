<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Aparelho;
use Illuminate\Http\Request;
use App\Exceptions\MazeException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use \stdClass;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\MazeHelper;

class UserController extends Controller
{
    public function index()
    {
        try
        {
            $User = User::OrderByDesc('created_at')->get();
            return response()->json($User, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar os usuarios', 500);
        }
    }

    public function show($id)
    {
        try
        {
            if(!$User = User::find($id))
            {
                throw new MazeException('Usuario não encontrado.', 404);
            }
        
            $User->chaves_ve;
            return response()->json($User, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível listar os usuários', 500);
        }
    }

    public function store(Request $request)
    {
        try
        {   
            $User = new User;
            $data = $request->all();
            
            // Formata a data se existir
            if (isset($data['birth_date'])) {
                $data['birth_date'] = Carbon::parse($data['birth_date'])->format('Y-m-d H:i:s');
            }
            
            $User->fill($data);
            $User->save();

            //Mail::send(new EnviarCadastro($retorno));

            return response()->json($User, 201);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar o Usuario', 500);
        }
    }

    public function store_app(Request $request)
    {
        try
        {   
            $user = new User;
            $data = $request->all();
            
            // Formata a data se existir
            if (isset($data['birth_date'])) {
                $data['birth_date'] = Carbon::parse($data['birth_date'])->format('Y-m-d H:i:s');
            }
            
            $user->fill($data);
            $user->save();

            $credentials = $request->only('login', 'password');
       
            try 
            {
                if (! $token = JWTAuth::attempt($credentials))
                {
                    throw new MazeException('Credenciais incorretas!', 400);
                }
            } 
            catch (JWTException $e) 
            {
                Log::error($e);
                throw new MazeException('Erro ao criar token!', 500);
            }

            $retorno = new stdClass;
            $retorno->user = Auth::user();
            $retorno->token = $token;

            return response()->json($retorno, 201);
        }
        catch(JWTException $e)
        {
            Log::error($e);
            throw new MazeException('Erro ao criar token!', 500);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar o Usuario', 500);
        }
    }

    public function aparelhoUser(Request $request)
    {
        try {\JWTAuth::parseToken()->authenticate();} catch (Exception $e) {};

        try {
            $user = Auth::user();

            $aparelho = Aparelho::firstOrCreate([
                'users_id' => $user->id,
                'identificador' => $request->input('identificador'),
                'tipo' => $request->input('tipo'),
            ]);
            
            $response = array();
            $response['user'] = $user;
            $response['aparelho'] = $aparelho;

            return response()->json([
                'data' => $response,
            ], 200);
        } catch (JWTException $e) {
            $message = [
                'exception' => $e->getMessage(),
                'title' => 'Erro interno de servidor',
                'message' => 'Não foi possível realizar o login devido a um erro interno',
            ];

            return response()->json($message, 500);
        }
    }

    public function store_com_foto(Request $request)
    {
        try
        {   
			
		
            $User = new User;
            $User->fill($request->all());
            

            if ($foto_usuario = $request->file('foto_usuario')) {
                $User->foto_usuario = MazeHelper::salva_arquivo_aws('usuarios', $foto_usuario, time() . '_' . $foto_usuario->getClientOriginalName());
            } 
            else {
                $url = 'https://via.placeholder.com/150';
                $User->foto_usuario = $url;
            }

            if ($foto_documento = $request->file('foto_documento')) {
                $User->foto_documento = MazeHelper::salva_arquivo_aws('usuarios', $foto_documento, time() . '_' . $foto_documento->getClientOriginalName());
            } 
            else {
                $url = 'https://via.placeholder.com/150';
                $User->foto_documento = $url;
            }

            $User->save();

            return response()->json($User, 201);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível cadastrar o Usuario', 500);
        }
    }

    public function update(Request $request, $id)
    {
        //try { \JWTAuth::parseToken()->authenticate(); } catch (Exception $e) {};

		try {
			
            if(!$User = User::find($id))
            {
                throw new MazeException('Usuário não encontrado.', 404);
            }

            $data = $request->all();
            
            // Formata a data se existir
            if (isset($data['birth_date'])) {
                $data['birth_date'] = Carbon::parse($data['birth_date'])->format('Y-m-d H:i:s');
            }

            $User->fill($data);
            $User->save();

            return response()->json($User, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar o Usuário', 500);
        }
    }

    public function atualiza_perfil(Request $request)
    {
        try { \JWTAuth::parseToken()->authenticate(); } catch (Exception $e) {};

		try {

            $user = Auth::user();

			$validator = Validator::make($request->all(), User::$rules_update, MazeHelper::get_mensagens_validacao());

			if($validator->fails())
			{
				throw new MazeException($validator->errors()->first(), 400);
			}
			
            if(!$usuario = User::find($user->id))
            {
                throw new MazeException('Usuário não encontrado.', 404);
            }

            $usuario->fill($request->all());
            $usuario->save();

            return response()->json($usuario, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível atualizar o Usuário', 500);
        }
    }

    public function delete($id)
    {
        try
        {
            if(!$User_retorno = User::find($id))
            {
                throw new MazeException('Usuário não encontrado.', 404);
            }

            $User = User::destroy($id);

            return response()->json($User, 200);
        }
        catch (MazeException $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            Log::error($e);
            throw new MazeException('Não foi possível deletar o Usuário', 500);
        }
    }

    public function usuarioPorNome(Request $request) {
		try {

			$user = User::where('name', $request->name)->firstOrFail();
            return response()->json($user, 200);
            
		} catch (MazeException $e) {
            throw $e;
        }

        catch (Exception $e) {
            Log::error($e);
            throw new MazeException('Não foi possível buscar o Usuário', 500);
        }
	}

    public function authenticateadm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ], MazeHelper::get_mensagens_validacao());

        if($validator->fails()){
            throw new MazeException($validator->errors()->first(), 400);
        }

        $credentials = $request->only('email', 'password');

        try 
        {
            if (! $token = JWTAuth::attempt($credentials))
            {
                throw new MazeException('Credenciais incorretas!', 400);
            }
        } 
        catch (JWTException $e) 
        {
            throw new MazeException('Erro ao criar token!', 500);
        }

        $user = Auth::user();

        if($user->tipo == 1){
            return response()->json(compact('token'));
        }
        else{
            throw new MazeException('Usuário não autorizado', 400);
        }
    }

    public function authenticateapp(Request $request)
    {
       
        $credentials = $request->only('login', 'password');
       // dd($credentials);
        try 
        {
            if (! $token = JWTAuth::attempt($credentials))
            {
                throw new MazeException('Credenciais incorretas!', 400);
            }
        } 
        catch (JWTException $e) 
        {
            Log::error($e);
            throw new MazeException('Erro ao criar token!', 500);
        }
        $retorno = new stdClass;
        $retorno->user = Auth::user();
        $retorno->token = $token;

        return response()->json($retorno);
        
    }


    public function email_contato(Request $request){
        $dados = new stdClass;
        $dados->email = $request->email;
        $dados->nome = $request->nome;
        $dados->assunto = $request->assunto;
        $dados->mensagem = $request->mensagem;
        Mail::send(new Contato($dados));

        return response()->json(
            [
                'data' => true
            ], 200);
    }


    private function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
     
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

}
