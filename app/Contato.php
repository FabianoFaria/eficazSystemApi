<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Contato extends Model
{
    //

	/**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table 		= 'cadastros_dados';

	protected $primaryKey 	= 'Cadastro_ID';

	public $timestamps 		= false;

	protected $fillable 	= ['Nome', 'Email', 'Data_Nascimento', 'Cpf_Cnpj'];

	public static $rules 	= array(
    	'Nome'				=>'required|min:2',
    	'Email'				=>'required|email|unique:cadastros_dados',
    	'Data_Nascimento' 	=> 'required|date_format:Y-m-d',
    	'Cpf_Cnpj' 			=> 'required|unique:cadastros_dados',
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
