<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Estabelecimento
 * 
 * @property int $id
 * @property string|null $nome
 * @property string|null $descricao
 * @property string|null $foto
 * @property string|null $phone
 * @property string|null $endereco
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $instagram
 * @property string|null $status
 * @property int $categorias_estabelecimentos_id
 * 
 * @property CategoriasEstabelecimento $categorias_estabelecimento
 * @property Collection|AtracoesEstabelecimento[] $atracoes_estabelecimentos
 * @property Collection|PesquisaEstabelecimento[] $pesquisa_estabelecimentos
 * @property Collection|Premio[] $premios
 * @property Collection|Sorteio[] $sorteios
 * @property Collection|VisualizacoesEstabelecimento[] $visualizacoes_estabelecimentos
 *
 * @package App\Models
 */
class Estabelecimento extends Model
{
	protected $table = 'estabelecimentos';
	public $timestamps = false;

	protected $casts = [
		'latitude' => 'float',
		'longitude' => 'float',
		'categorias_estabelecimentos_id' => 'int'
	];

	protected $fillable = [
		'nome',
		'descricao',
		'foto',
		'phone',
		'endereco',
		'latitude',
		'longitude',
		'instagram',
		'status',
		'categorias_estabelecimentos_id'
	];

	public function categorias_estabelecimento()
	{
		return $this->belongsTo(CategoriasEstabelecimento::class, 'categorias_estabelecimentos_id');
	}

	public function atracoes_estabelecimentos()
	{
		return $this->hasMany(AtracoesEstabelecimento::class, 'estabelecimentos_id');
	}

	public function pesquisa_estabelecimentos()
	{
		return $this->hasMany(PesquisaEstabelecimento::class, 'estabelecimentos_id');
	}

	public function premios()
	{
		return $this->hasMany(Premio::class, 'estabelecimentos_id');
	}

	public function sorteios()
	{
		return $this->hasMany(Sorteio::class, 'estabelecimentos_id');
	}

	public function visualizacoes_estabelecimentos()
	{
		return $this->hasMany(VisualizacoesEstabelecimento::class, 'estabelecimentos_id');
	}
}
