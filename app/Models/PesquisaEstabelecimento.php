<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PesquisaEstabelecimento
 * 
 * @property int $id
 * @property int $perguntas_estabelecimento_id
 * @property int $estabelecimentos_id
 * 
 * @property Estabelecimento $estabelecimento
 * @property PerguntasEstabelecimento $perguntas_estabelecimento
 *
 * @package App\Models
 */
class PesquisaEstabelecimento extends Model
{
	protected $table = 'pesquisa_estabelecimento';
	public $timestamps = false;

	protected $casts = [
		'perguntas_estabelecimento_id' => 'int',
		'estabelecimentos_id' => 'int'
	];

	protected $fillable = [
		'perguntas_estabelecimento_id',
		'estabelecimentos_id'
	];

	public function estabelecimento()
	{
		return $this->belongsTo(Estabelecimento::class, 'estabelecimentos_id');
	}

	public function perguntas_estabelecimento()
	{
		return $this->belongsTo(PerguntasEstabelecimento::class);
	}
}
