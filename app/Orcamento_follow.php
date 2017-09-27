<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Orcamento_follow extends Model
{
    //Esse model servirá para salvar a descrição do orçamento que fica em uma tabela diferente da tabela de orçamento

    /**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table 		= 'orcamentos_follows';

	protected $primaryKey 	= 'Follow_ID';

	public $timestamps 		= false;

	protected $fillable 	= ['Titulo'];

	public static $rules 	= array(
    	'Titulo'				=>'required|min:4',
    );
}
