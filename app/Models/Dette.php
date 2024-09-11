<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;
  
  protected $fillable = ['client_id', 'montant'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'detail_dette')
                    ->withPivot('qteVente', 'prixVente')
                    ->withTimestamps();
    }


    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

}
