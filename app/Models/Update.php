<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Update extends Model
{
    protected $fillable = [
        'title',
        'version',
        'summary',
        'release_date',
        'status',
        'published_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<UpdateItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(UpdateItem::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  Builder<Update>  $query
     * @return Builder<Update>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at');
    }
}
