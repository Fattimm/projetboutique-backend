<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = ['dette_id', 'montant'];

    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }
}
