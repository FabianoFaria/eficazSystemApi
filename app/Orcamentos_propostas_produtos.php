<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orcamentos_propostas_produtos extends Model
{
    //
     protected $table 		= 'orcamentos_propostas_produtos';

	protected $primaryKey 	= 'Proposta_Produto_ID';

	public $timestamps 		= false;

	protected $fillable 	= ['Titulo'];

	public static $rules 	= array(
    	'Titulo'				=>'required|min:4',
    );
}
