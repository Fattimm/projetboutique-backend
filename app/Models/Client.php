<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

   // public mixed $user_id;
    protected $fillable = [
        'surname',
        'adresse',
        'telephone',
        'user_id'
    ];
    protected $hidden = [
        //  'password',
        'created_at',
        'updated_at',
    ];

    function user() {
        return $this->belongsTo(User::class);
    }
    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('filterByTelephone', function (Builder $builder) {
            if (request()->has('telephone')) {
                $builder->where('telephone', 'like', '%' . request()->input('telephone') . '%');
            }
        });
    }
}
