<?php

namespace App\Http\Controllers;

use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging;
use App\Models\Aparelho;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Validator;
use App\Models\Estabelecimento;
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

    public function envia_notificacao(Request $request) {
		try {
			
            $usuarios = $request->all();
            
            $qtd = (count($usuarios['usuario']));

            $retorno = [];
            
            for ($i=0; $i<$qtd;$i++) {

				$user = User::where('id', $usuarios['usuario'][$i])->first();

				if(isset($user->aparelhos)){

					$aparelhos = $user->aparelhos->pluck('identificador')->toArray();
					if(count($aparelhos) > 0 ){
						foreach ($aparelhos as $aparelho) {
							Log::info('Enviando notificação para token: ' . $aparelho);
							$retorno = $this->enviar_notificacao($usuarios['titulo'],$usuarios['mensagem'], [$aparelho]);
							Log::info('Resposta da notificação: ' . json_encode($retorno));
						}
					}
				}
			}
			
			return response()->json([
				'data'=> $retorno
			],200);
		} catch (Exception $e) {
			\DB::rollBack();
            \Log::info(get_class($e).' | '.$e->getMessage());
            return response()->json([
                'title'=>'Erro desconhecido',
                'message'=>'Erro ao enviar notificações.',
                'exception'=>$e->getMessage()
            ],500);
        }
    }

    public function enviar_notificacao($titulo, $mensagem, $tokens)
    {
        try {
            // Configurar o Firebase diretamente
            $factory = (new \Kreait\Firebase\Factory())
                ->withServiceAccount(base_path('firebase.json'))
                ->withProjectId('baitasorte');

            $messaging = $factory->createMessaging();

            $notification = FirebaseNotification::fromArray([
                'title' => $titulo,
                'body' => $mensagem
            ]);

            // If tokens is a single string, convert it to array
            if (!is_array($tokens)) {
                $tokens = [$tokens];
            }

            // Track invalid tokens for cleanup
            $invalidTokens = [];

            // Send message to each token
            $responses = [];
            foreach ($tokens as $token) {
                try {
                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification($notification)
                        ->withData([
                            'sound' => 'default',
                            'icon' => 'www/img/icones/android-icon-48x48.png'
                        ]);

                    $response = $messaging->send($message);
                    $responses[] = $response;
                } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                    // Token is invalid, add to cleanup list
                    Log::info('Invalid token detected: ' . $token);
                    $invalidTokens[] = $token;
                } catch (\Exception $e) {
                    Log::error('Error sending notification to token ' . $token . ': ' . $e->getMessage());
                    throw $e;
                }
            }

            // Cleanup invalid tokens from database
            if (!empty($invalidTokens)) {
                UsersDevice::whereIn('device_id', $invalidTokens)->delete();
                Log::info('Cleaned up ' . count($invalidTokens) . ' invalid tokens from database');
            }

            return $responses;
        } catch (\Exception $e) {
            Log::error('Error in enviar_notificacao: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calcula a distância em metros entre dois pontos usando latitude e longitude
     * @param float $lat1 Latitude do ponto 1
     * @param float $lon1 Longitude do ponto 1
     * @param float $lat2 Latitude do ponto 2
     * @param float $lon2 Longitude do ponto 2
     * @return float Distância em metros
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Raio da Terra em metros
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

    /**
     * Calcula a distância entre o usuário e um estabelecimento
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcularDistanciaEstabelecimento(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'estabelecimentos_id' => 'required|integer|exists:estabelecimentos,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $estabelecimento = Estabelecimento::find($request->estabelecimentos_id);
            
            if (!$estabelecimento) {
                return response()->json(['error' => 'Estabelecimento não encontrado'], 404);
            }

            // Adicionar log do conteúdo completo do iframe
            Log::info('Conteúdo do iframe: ' . $estabelecimento->mapa);

            // Extrair latitude e longitude do parâmetro pb do iframe do Google Maps
            preg_match('/pb=!1m17!1m12!1m3!1d(.*?)!2d(.*?)!3d(.*?)!/', $estabelecimento->mapa, $matches);
            
            Log::info('Matches encontrados: ' . print_r($matches, true));
            
            if (empty($matches[2]) || empty($matches[3])) {
                // Tentar extrair usando uma expressão regular mais simples
                preg_match('/!2d(.*?)!3d(.*?)!/', $estabelecimento->mapa, $matches2);
                
                Log::info('Tentando extrair com regex alternativa: ' . print_r($matches2, true));
                
                if (empty($matches2[1]) || empty($matches2[2])) {
                    return response()->json(['error' => 'Localização do estabelecimento não encontrada no iframe', 'iframe_content' => $estabelecimento->mapa], 400);
                }
                
                $estabelecimentoLat = floatval($matches2[2]);
                $estabelecimentoLon = floatval($matches2[1]);
            } else {
                $estabelecimentoLat = floatval($matches[3]);
                $estabelecimentoLon = floatval($matches[2]);
            }

            // Adicionar log para debug
            Log::info('Coordenadas do estabelecimento: Lat=' . $estabelecimentoLat . ' Lon=' . $estabelecimentoLon);

            $distancia = $this->calcularDistancia(
                $request->latitude,
                $request->longitude,
                $estabelecimentoLat,
                $estabelecimentoLon
            );

            $dentroDoRaio = $distancia <= 300;

            Log::info('Distância: ' . $distancia);
            if($dentroDoRaio){
                return response()->json([
                    'dentro_do_raio' => $dentroDoRaio,
                    'distancia' => $distancia,
                    'unidade' => 'metros',
                ]);
            }

            return response()->json(['error' => 'Fora do raio', 'distancia' => $distancia], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
