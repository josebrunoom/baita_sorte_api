<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AlternativasPerguntum
 * 
 * @property int $id
 * @property string|null $texto
 * @property string|null $status
 * @property string|null $certa_errada
 * @property int $perguntas_estabelecimento_id
 * 
 * @property PerguntasEstabelecimento $perguntas_estabelecimento
 * @property Collection|RespostasPergunta[] $respostas_perguntas
 *
 * @package App\Models
 */
class AlternativasPerguntum extends Model
{
	protected $table = 'alternativas_pergunta';
	public $timestamps = false;

	protected $casts = [
		'perguntas_estabelecimento_id' => 'int'
	];

	protected $fillable = [
		'texto',
		'status',
		'certa_errada',
		'perguntas_estabelecimento_id'
	];

	public function perguntas_estabelecimento()
	{
		return $this->belongsTo(PerguntasEstabelecimento::class);
	}

	public function respostas_perguntas()
	{
		return $this->hasMany(RespostasPergunta::class, 'alternativas_pergunta_id');
	}
}
