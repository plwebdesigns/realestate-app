<?php

namespace App\Models;

use Database\Factories\LeadStatusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'is_active', 'is_default'])]
class LeadStatus extends Model
{
    /** @use HasFactory<LeadStatusFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (LeadStatus $status): void {
            if (! $status->is_default) {
                return;
            }

            static::query()
                ->whereKeyNot($status->getKey())
                ->where('is_default', true)
                ->update(['is_default' => false]);
        });
    }

    public static function defaultId(): ?int
    {
        return static::query()
            ->active()
            ->where('is_default', true)
            ->value('id');
    }

    /**
     * @return HasMany<Lead, $this>
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * @param  Builder<LeadStatus>  $query
     * @return Builder<LeadStatus>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
