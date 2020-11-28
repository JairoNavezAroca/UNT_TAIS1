<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Varios extends Model
{
	protected $table = 'varios';
	protected $primaryKey = 'idVarios';
	public $timestamps = false;
	protected $fillable = [
		'idVarios',
		'tabla',
		'campo',
		'valor',
		'significado'
	];
}
