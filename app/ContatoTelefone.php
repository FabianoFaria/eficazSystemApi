<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;


class ContatoTelefone extends Model
{

	/**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table 		= 'cadastros_telefones';

	protected $primaryKey 	= 'Cadastro_Telefone_ID';

	public $timestamps 		= false;

	protected $fillable 	= ['Cadastro_ID', 'Telefone', 'Observacao'];

	public static $rules 	= array(
    	'Telefone'=> 'required|numeric|min:8'
    );

	public $errors;

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