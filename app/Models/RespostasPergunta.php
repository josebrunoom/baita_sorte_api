<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RespostasPergunta
 * 
 * @property int $id
 * @property int $perguntas_estabelecimento_id
 * @property int $users_id
 * @property int $alternativas_pergunta_id
 * 
 * @property AlternativasPerguntum $alternativas_perguntum
 * @property PerguntasEstabelecimento $perguntas_estabelecimento
 * @property User $user
 *
 * @package App\Models
 */
class RespostasPergunta extends Model
{
	protected $table = 'respostas_perguntas';
	public $timestamps = false;

	protected $casts = [
		'perguntas_estabelecimento_id' => 'int',
		'users_id' => 'int',
		'alternativas_pergunta_id' => 'int'
	];

	protected $fillable = [
		'perguntas_estabelecimento_id',
		'users_id',
		'alternativas_pergunta_id'
	];

	public function alternativas_perguntum()
	{
		return $this->belongsTo(AlternativasPerguntum::class, 'alternativas_pergunta_id');
	}

	public function perguntas_estabelecimento()
	{
		return $this->belongsTo(PerguntasEstabelecimento::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'users_id');
	}
}
