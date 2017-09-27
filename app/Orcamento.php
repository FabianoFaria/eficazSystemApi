<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Orcamento extends Model
{
	/**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table 		= 'orcamentos_workflows';

	protected $primaryKey 	= 'Workflow_ID';

	public $timestamps 		= false;

	protected $fillable 	= ['Titulo'];

	public static $rules 	= array(
    	'Titulo'				=>'required|min:4',
    );

	public function isValid($data){

    	//FAZENDO A VALIDAÇÃO COM OS ATRIBUTOS DO PROPRIO OBJETO
    	//$validacao = Validator::Make($this->attributes, static::$rules);

    	$validacao = Validator::Make($data, static::$rules);

    	if($validacao->passes()){

    		return true;

    	}else{

    		$this->errors = $validacao->messages();

    		return false;

    	}

    }
}
