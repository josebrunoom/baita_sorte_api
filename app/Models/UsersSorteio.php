<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersSorteio
 * 
 * @property int $id
 * @property string|null $status
 * @property int $users_id
 * @property int $sorteios_id
 * @property int $pesquisa_estabelecimento_id
 *
 * @package App\Models
 */
class UsersSorteio extends Model
{
	protected $table = 'users_sorteio';
	public $timestamps = false;

	protected $casts = [
		'users_id' => 'int',
		'sorteios_id' => 'int',
		'pesquisa_estabelecimento_id' => 'int'
	];

	protected $fillable = [
		'status',
		'users_id',
		'sorteios_id',
		'pesquisa_estabelecimento_id'
	];
}
