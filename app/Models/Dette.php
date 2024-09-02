<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;

    // Définir les attributs modifiables
    protected $fillable = ['client_id', 'date', 'montant', 'montantDu', 'montantRestant'];

    // Définir les attributs cachés
    protected $hidden = ['created_at', 'updated_at']; // Masquer les timestamps si nécessaire

    // Définir la relation inverse avec le modèle Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

}
