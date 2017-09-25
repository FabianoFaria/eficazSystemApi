<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class ContatoEndereco extends Model
{
 	
	/**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table 		= 'cadastros_enderecos';

	protected $primaryKey 	= 'Cadastro_Endereco_ID';

	public $timestamps 		= false;

	protected $fillable 	= ['Logradouro','Numero','Complemento','Bairro','Cidade','UF', 'CEP'];

	public static $rules 	= array(
		'Logradouro'=> 'required|min:2',
        'Bairro'=> 'required|min:2',
    	'Cidade'=>'required',
    	'UF'=>'required',
    	'CEP'=>'required|min:8|max:9'
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
