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
    	'Titulo'			=>'required|min:4',
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


    // Retorna o valor da comisão, já com os valores de imposto e porcentagem descontado

    public static function comissaoOrcamentoAulso($valorTotalOrcamento){

        /*
            preço liquido = Preço final - 18% de imposto
            total a pagar = preço liquido - 10%
        */

        $valorLiquido =  $valorTotalOrcamento - (($valorTotalOrcamento / 100) * 18);
        $valorComisao =  ($valorLiquido / 100) * 10;

        return $valorComisao;

    }

    public static function verificaPagamentoFimDeSemana($dataOriginal){

        $dateTemp   = $dataOriginal;

        $date       = date("l", $dateTemp);

        switch ($date) {
            case 'Saturday':
            
                $dateTemp = strtotime($dataOriginal." + 2 days");

                return $dateTemp;

            break;

            case 'Sunday':

                $dateTemp = strtotime($dataOriginal." + 1 days");

                return $dateTemp;

            break;
                                
            default:
                                    
                return $dateTemp;

            break;
        }

    }



}
