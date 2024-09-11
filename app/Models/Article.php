<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'prix',
        'qteStock',
    ];
    protected $hidden = [
        'created_at' , 
        'updated_at',
    ];

    public function filter($criteria)
    {
        // Implémentez une logique de filtrage basée sur les critères
        return $this->where($criteria)->get();
    }
    
    public function scopeFilterByLibelle($query, $libelle)
    {
        return $query->where('libelle', 'like', "%$libelle%");
    }

    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'detail_dette')
                    ->withPivot('qteVente', 'prixVente')
                    ->withTimestamps();
    }


}