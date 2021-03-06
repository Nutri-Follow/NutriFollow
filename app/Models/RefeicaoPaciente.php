<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefeicaoPaciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'foto',
        'observacoes',
        'refeicao_id',
        'paciente_id',
        'refeicao_referencia_id'
    ];

    public function refeicao()
    {
        return $this->belongsTo(Refeicao::class);
    }
}
