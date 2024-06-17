<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
	public $timestamps = false;
	protected $table = 'users';

	protected $casts = [
		'birth_date' => 'datetime'
	];

	protected $hidden = [
		'password'
	];

	static public $rules_post = [
        "login" => ['nullable', 'unique:users,login']
    ];
    static public $rules_update = [
        
    ];

	protected $fillable = [
		'nome',
		'login',
		'password',
		'phone',
		'genero',
		'birth_date'
	];
	
	public function setPasswordAttribute($password)
    {
        if ( !empty($password) ) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

	public function respostas_perguntas()
	{
		return $this->hasMany(RespostasPergunta::class, 'users_id');
	}

	public function visualizacoes_estabelecimentos()
	{
		return $this->hasMany(VisualizacoesEstabelecimento::class, 'users_id');
	}

	public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'usr'=> [
                'nome' 	                    => $this->nome,
                'id' 		                => $this->id,
                'login' 		            => $this->login,
            ]
        ];
    }
}
