<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'id_number'
    ];

    /**
     * Get all of the bills for the Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
