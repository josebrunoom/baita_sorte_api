<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VisualizacoesEstabelecimento
 * 
 * @property int $id
 * @property int $users_id
 * @property int $estabelecimentos_id
 * 
 * @property User $user
 * @property Estabelecimento $estabelecimento
 *
 * @package App\Models
 */
class VisualizacoesEstabelecimento extends Model
{
	protected $table = 'visualizacoes_estabelecimento';
	public $timestamps = false;

	protected $casts = [
		'users_id' => 'int',
		'estabelecimentos_id' => 'int'
	];

	protected $fillable = [
		'users_id',
		'estabelecimentos_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'users_id');
	}

	public function estabelecimento()
	{
		return $this->belongsTo(Estabelecimento::class, 'estabelecimentos_id');
	}
}
