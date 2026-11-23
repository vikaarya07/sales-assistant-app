<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'phone_normalized',
        'name',
        'contract_number',
        'amount',
        'branch',
        'source',
        'status',
        'last_contacted_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'last_contacted_at' => 'datetime',
            'status' => CustomerStatus::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<CustomerStatusAssignment, $this>
     */
    public function statusAssignments(): HasMany
    {
        return $this->hasMany(CustomerStatusAssignment::class);
    }

    /**
     * @param  array<int, string|CustomerStatus>  $statuses
     */
    public function syncStatuses(array $statuses): void
    {
        $statusValues = collect($statuses)
            ->map(function ($status): string {
                return $status instanceof CustomerStatus
                    ? $status->value
                    : (string) $status;
            })
            ->unique()
            ->values();

        foreach ($statusValues as $status) {
            if (CustomerStatus::tryFrom($status) === null) {
                throw new \InvalidArgumentException(
                    "Status customer tidak valid: {$status}"
                );
            }
        }

        if ($statusValues->isEmpty()) {
            throw new \InvalidArgumentException(
                'Minimal satu status customer harus dipilih.'
            );
        }

        DB::transaction(function () use ($statusValues): void {
            $this->statusAssignments()->delete();

            foreach ($statusValues as $status) {
                $this->statusAssignments()->create([
                    'status' => $status,
                ]);
            }

            $currentStatus = (string) $this->getRawOriginal('status');

            $primaryStatus = $statusValues->contains($currentStatus)
                ? $currentStatus
                : $statusValues->first();

            $this->update([
                'status' => $primaryStatus,
            ]);
        });
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format(
            $this->amount,
            0,
            ',',
            '.'
        );
    }

    public function getWhatsappUrlAttribute(): string
    {
        return 'https://wa.me/'.$this->phone_normalized;
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    public function replaceTemplateVariables(
        string $template,
        array $variables = []
    ): string {
        $replacements = [
            '{{nama}}' => $this->name,
            '{{nomor_kontrak}}' => $this->contract_number,
            '{{nominal}}' => number_format($this->amount, 0, ',', '.'),
            '{{cabang}}' => $this->branch,
        ];

        $replacements = array_merge(
            $replacements,
            $variables
        );

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
    }

    public function whatsappUrlWithMessage(string $message): string
    {
        return 'https://api.whatsapp.com/send?phone='
            .$this->phone_normalized
            .'&text='
            .rawurlencode($message);
    }
}
