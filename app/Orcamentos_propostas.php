<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orcamentos_propostas extends Model
{
    //
    protected $table 		= 'orcamentos_propostas';

	protected $primaryKey 	= 'Proposta_ID';

	public $timestamps 		= false;

	protected $fillable 	= ['Titulo'];

	public static $rules 	= array(
    	'Titulo'				=>'required|min:4',
    );
}
