<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'experience_years',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'experience_years' => 'int',
    ];

    public function huntingBookings(): HasMany
    {
        return $this->hasMany(HuntingBooking::class);
    }
}
