<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orcamentos_propostas_vencimentos extends Model
{
    //
    protected $table 		= 'orcamentos_propostas_vencimentos';

	protected $primaryKey 	= 'Orcamento_Proposta_Vencimento_ID';

	public $timestamps 		= false;

	protected $fillable 	= ['Titulo'];

	public static $rules 	= array(
    	'Titulo'				=>'required|min:4',
    );
}
