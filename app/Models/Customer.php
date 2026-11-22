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
     * @param  array<int, string>  $statuses
     */
    public function syncStatuses(array $statuses): void
    {
        $values = collect($statuses)
            ->unique()
            ->values()
            ->all();

        if ($values === []) {
            throw new \InvalidArgumentException(
                'Customer harus memiliki minimal satu status.'
            );
        }

        foreach ($values as $value) {
            if (CustomerStatus::tryFrom($value) === null) {
                throw new \InvalidArgumentException(
                    'Status customer tidak valid.'
                );
            }
        }

        DB::transaction(function () use ($values): void {
            $currentPrimary = $this->status;

            $primary = in_array($currentPrimary, $values, true)
                ? $currentPrimary
                : $values[0];

            $this->statusAssignments()->delete();

            $this->statusAssignments()->createMany(
                array_map(
                    fn (string $value): array => ['status' => $value],
                    $values
                )
            );

            $this->status = $primary;
            $this->save();
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
