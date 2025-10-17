<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HuntingBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_name',
        'hunter_name',
        'guide_id',
        'date',
        'participants_count',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'participants_count' => 'int',
        'guide_id' => 'int',
    ];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }
}
