<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = ['marca_id', 'nome', 'imagem', 'numero_portas', 'lugares', 'air_bag', 'abs'];

    public function rules() {
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
            'numero_portas' => 'required|integer|digits_between:1,5', //(1,2,3,4,5)
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required:boolean',
            'abs' => 'required|boolean' //true, false, 1, 0, "1", "0"
        ];

        /*
            1) tabela
            2) nome da coluna que será pesquisada na tabela
            3) Id do registro que será desconsiderado na pesquisa
        */
    }

    public function feedback(){
        return [
            'marca_id.exists' => 'Esta marca não existe, por gentileza informar outra marca',
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'Esse modelo já existe, por gentileza informar outro modelo',
            'numero_portas.integer' => 'O campo número de portas só aceita números inteiros como entrada',
            'lugares.integer' => 'O campo lugares só aceita números inteiros como entrada'
        ];
    }

    public function marca() {
        // UM modelo PERTENCE a uma marca
        return $this->belongsTo('App\Models\Marca');
    }
}
