<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersDevice
 * 
 * @property int $id
 * @property string|null $device_id
 * @property int $users_id
 *
 * @package App\Models
 */
class UsersDevice extends Model
{
	protected $table = 'users_device';
	public $timestamps = false;

	protected $casts = [
		'users_id' => 'int'
	];

	protected $fillable = [
		'device_id',
		'users_id'
	];
}
