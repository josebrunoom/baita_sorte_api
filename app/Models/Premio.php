<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Premio
 * 
 * @property int $id
 * @property string|null $nome
 * @property string|null $descricao
 * @property string|null $foto
 * @property int $sorteios_id
 * @property int $estabelecimentos_id
 * 
 * @property Estabelecimento $estabelecimento
 * @property Sorteio $sorteio
 *
 * @package App\Models
 */
class Premio extends Model
{
	protected $table = 'premios';
	public $timestamps = false;

	protected $casts = [
		'sorteios_id' => 'int',
		'estabelecimentos_id' => 'int'
	];

	protected $fillable = [
		'nome',
		'descricao',
		'foto',
		'sorteios_id',
		'estabelecimentos_id'
	];

	public function estabelecimento()
	{
		return $this->belongsTo(Estabelecimento::class, 'estabelecimentos_id');
	}

	public function sorteio()
	{
		return $this->belongsTo(Sorteio::class, 'sorteios_id');
	}
}
