<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];

    /** @var string[] model casts */
    protected $casts = [
        'event_date' => 'datetime',
    ];

    /**
     * An appointment belongs to a user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Query scope for finished appointments.
     * @param Builder $query
     * @return Builder
     */
    public function scopePast(Builder $query) : Builder
    {
        return $query->where('event_date', '<', now());
    }

    /**
     * Query scope for unfinished appointments.
     * @param Builder $query
     * @return Builder
     */
    public function scopeFuture(Builder $query) : Builder
    {
        return $query->where('event_date', '>', now());
    }
}
