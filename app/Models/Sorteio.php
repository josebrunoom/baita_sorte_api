<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sorteio
 * 
 * @property int $id
 * @property string|null $nome
 * @property string|null $descricao
 * @property Carbon|null $data_inicio
 * @property Carbon|null $data_fim
 * @property string|null $status
 * @property int $estabelecimentos_id
 * 
 * @property Estabelecimento $estabelecimento
 * @property Collection|Premio[] $premios
 *
 * @package App\Models
 */
class Sorteio extends Model
{
	protected $table = 'sorteios';
	public $timestamps = false;

	protected $casts = [
		'data_inicio' => 'datetime',
		'data_fim' => 'datetime',
		'estabelecimentos_id' => 'int'
	];

	protected $fillable = [
		'nome',
		'descricao',
		'data_inicio',
		'data_fim',
		'status',
		'estabelecimentos_id'
	];

	public function estabelecimento()
	{
		return $this->belongsTo(Estabelecimento::class, 'estabelecimentos_id');
	}

	public function premios()
	{
		return $this->hasMany(Premio::class, 'sorteios_id');
	}
}
