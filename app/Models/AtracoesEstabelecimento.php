<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AtracoesEstabelecimento
 * 
 * @property int $id
 * @property string|null $nome
 * @property string|null $descricao
 * @property Carbon|null $data_atracao
 * @property Carbon|null $inicio_divulgacao
 * @property Carbon|null $fim_divulgacao
 * @property int $estabelecimentos_id
 * 
 * @property Estabelecimento $estabelecimento
 *
 * @package App\Models
 */
class AtracoesEstabelecimento extends Model
{
	protected $table = 'atracoes_estabelecimento';
	public $timestamps = false;

	protected $casts = [
		'data_atracao' => 'datetime',
		'inicio_divulgacao' => 'datetime',
		'fim_divulgacao' => 'datetime',
		'estabelecimentos_id' => 'int'
	];

	protected $fillable = [
		'nome',
		'descricao',
		'data_atracao',
		'inicio_divulgacao',
		'fim_divulgacao',
		'estabelecimentos_id'
	];

	public function estabelecimento()
	{
		return $this->belongsTo(Estabelecimento::class, 'estabelecimentos_id');
	}
}
