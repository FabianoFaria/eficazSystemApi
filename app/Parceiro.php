<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Parceiro extends Model
{
    //
    /**
	* The database table used by the model.
	*
	* @var string
	*/

	protected $table 		= 'sistema_parceiros';

	protected $primaryKey 	= 'Parceiro_ID';

	public $timestamps 		= false;

	protected $fillable 	= ['Nome_Parceiro'];

	public static $rules 	= array(
    	'Nome_Parceiro'				=>'required|min:2'
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
