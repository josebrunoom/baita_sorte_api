<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CategoriasEstabelecimento
 * 
 * @property int $id
 * @property string|null $nome
 * @property string|null $icone
 * @property string|null $descricao
 * 
 * @property Collection|Estabelecimento[] $estabelecimentos
 *
 * @package App\Models
 */
class CategoriasEstabelecimento extends Model
{
	protected $table = 'categorias_estabelecimentos';
	public $timestamps = false;

	protected $fillable = [
		'nome',
		'icone',
		'descricao'
	];

	public function estabelecimentos()
	{
		return $this->hasMany(Estabelecimento::class, 'categorias_estabelecimentos_id');
	}
}
