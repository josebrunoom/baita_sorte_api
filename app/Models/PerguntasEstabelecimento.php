<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PerguntasEstabelecimento
 * 
 * @property int $id
 * @property string|null $texto
 * @property string|null $tipo
 * @property string|null $status
 * 
 * @property Collection|AlternativasPerguntum[] $alternativas_pergunta
 * @property Collection|PesquisaEstabelecimento[] $pesquisa_estabelecimentos
 * @property Collection|RespostasPergunta[] $respostas_perguntas
 *
 * @package App\Models
 */
class PerguntasEstabelecimento extends Model
{
	protected $table = 'perguntas_estabelecimento';
	public $timestamps = false;

	protected $fillable = [
		'texto',
		'tipo',
		'status'
	];

	public function alternativas_pergunta()
	{
		return $this->hasMany(AlternativasPerguntum::class);
	}

	public function pesquisa_estabelecimentos()
	{
		return $this->hasMany(PesquisaEstabelecimento::class);
	}

	public function respostas_perguntas()
	{
		return $this->hasMany(RespostasPergunta::class);
	}
}
