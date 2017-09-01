<?php

namespace App\Transformer;

//namespace App\Http\Transformers;

 
class ContatoTransformer {
 
    public function transform($contato) {
        return [
            'id' => $contato->Cadastro_ID,
            'nome' => $contato->Nome,
            'nome_fantasia' => $contato->Nome_Fantasia
        ];
    }
}
