<?php

declare(strict_types=1);

namespace LaravelInteraction\Block;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use LaravelInteraction\Block\Events\Blocked;
use LaravelInteraction\Block\Events\Unblocked;

/**
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $blocker
 * @property \Illuminate\Database\Eloquent\Model $blockable
 *
 * @method static \LaravelInteraction\Block\Block|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Block\Block|\Illuminate\Database\Eloquent\Builder query()
 */
class Block extends MorphPivot
{
    /**
     * @var array<string, class-string<\LaravelInteraction\Block\Events\Blocked>>|array<string, class-string<\LaravelInteraction\Block\Events\Unblocked>>
     */
    protected $dispatchesEvents = [
        'created' => Blocked::class,
        'deleted' => Unblocked::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(
            function (self $like): void {
                if ($like->uuids()) {
                    $like->{$like->getKeyName()} = Str::orderedUuid();
                }
            }
        );
    }

    /**
     * @var bool
     */
    public $incrementing = true;

    public function getIncrementing(): bool
    {
        if ($this->uuids()) {
            return false;
        }

        return parent::getIncrementing();
    }

    public function getKeyName(): string
    {
        return $this->uuids() ? 'uuid' : parent::getKeyName();
    }

    public function getKeyType(): string
    {
        return $this->uuids() ? 'string' : parent::getKeyType();
    }

    public function getTable(): string
    {
        return config('block.table_names.blocks') ?: parent::getTable();
    }

    public function isBlockedBy(Model $user): bool
    {
        return $user->is($this->blocker);
    }

    public function isBlockedTo(Model $object): bool
    {
        return $object->is($this->blockable);
    }

    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('blockable_type', app($type)->getMorphClass());
    }

    public function blockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function blocker(): BelongsTo
    {
        return $this->user();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('block.models.user'), config('block.column_names.user_foreign_key'));
    }

    protected function uuids(): bool
    {
        return (bool) config('block.uuids');
    }
}
