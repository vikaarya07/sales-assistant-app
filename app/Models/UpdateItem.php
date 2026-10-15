<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpdateItem extends Model
{
    protected $fillable = [
        'update_id',
        'category',
        'title',
        'description',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Update, $this>
     */
    public function release(): BelongsTo
    {
        return $this->belongsTo(Update::class, 'update_id');
    }
}
