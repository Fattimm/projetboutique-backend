<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paiement extends Model
{
    use SoftDeletes;

    protected $fillable = ['dette_id', 'montant'];

    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }
}
