<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LaravelInteraction\Support\Interaction;
use function is_a;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Block\Block[] $blockableBlocks
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Block\Concerns\Blocker[] $blockers
 * @property-read string|int|null $blockers_count
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereBlockedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotBlockedBy(\Illuminate\Database\Eloquent\Model $user)
 */
trait Blockable
{
    public function isNotBlockedBy(Model $user): bool
    {
        return ! $this->isBlockedBy($user);
    }

    public function isBlockedBy(Model $user): bool
    {
        if (! is_a($user, config('block.models.user'))) {
            return false;
        }

        $blockersLoaded = $this->relationLoaded('blockers');

        if ($blockersLoaded) {
            return $this->blockers->contains($user);
        }

        return ($this->relationLoaded('blockableBlocks') ? $this->blockableBlocks : $this->blockableBlocks())
            ->where(config('block.column_names.user_foreign_key'), $user->getKey())
            ->count() > 0;
    }

    public function scopeWhereNotBlockedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'blockers',
            static function (Builder $query) use ($user): Builder {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function scopeWhereBlockedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'blockers',
            static function (Builder $query) use ($user): Builder {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function blockableBlocks(): MorphMany
    {
        return $this->morphMany(config('block.models.pivot'), 'blockable');
    }

    public function blockers(): BelongsToMany
    {
        return $this->morphToMany(
            config('block.models.user'),
            'blockable',
            config('block.models.pivot'),
            null,
            config('block.column_names.user_foreign_key')
        )->withTimestamps();
    }

    public function blockersCount(): int
    {
        if ($this->blockers_count !== null) {
            return (int) $this->blockers_count;
        }

        $this->loadCount('blockers');

        return (int) $this->blockers_count;
    }

    /**
     * @phpstan-param 1|2|3|4 $mode
     *
     * @param array<int, string>|null $divisors
     */
    public function blockersCountForHumans(int $precision = 1, int $mode = PHP_ROUND_HALF_UP, $divisors = null): string
    {
        return Interaction::numberForHumans(
            $this->blockersCount(),
            $precision,
            $mode,
            $divisors ?? config('block.divisors')
        );
    }
}
