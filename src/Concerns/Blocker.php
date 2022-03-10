<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use LaravelInteraction\Block\Block;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Block\Block[] $blockerBlocks
 * @property-read int|null $blocker_blocks_count
 */
trait Blocker
{
    public function hasNotBlocked(Model $object): bool
    {
        return ! $this->hasBlocked($object);
    }

    public function hasBlocked(Model $object): bool
    {
        return ($this->relationLoaded('blockerBlocks') ? $this->blockerBlocks : $this->blockerBlocks())
            ->where('blockable_id', $object->getKey())
            ->where('blockable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function block(Model $object): Block
    {
        $attributes = [
            'blockable_id' => $object->getKey(),
            'blockable_type' => $object->getMorphClass(),
        ];

        return $this->blockerBlocks()
            ->where($attributes)
            ->firstOr(function () use ($attributes) {
                $blockerBlocksLoaded = $this->relationLoaded('blockerBlocks');
                if ($blockerBlocksLoaded) {
                    $this->unsetRelation('blockerBlocks');
                }

                return $this->blockerBlocks()
                    ->create($attributes);
            });
    }

    public function blockerBlocks(): HasMany
    {
        return $this->hasMany(config('block.models.pivot'), config('block.column_names.user_foreign_key'));
    }

    /**
     * @return bool|\LaravelInteraction\Block\Block
     */
    public function toggleBlock(Model $object)
    {
        return $this->hasBlocked($object) ? $this->unblock($object) : $this->block($object);
    }

    public function unblock(Model $object): bool
    {
        $hasNotBlocked = $this->hasNotBlocked($object);
        if ($hasNotBlocked) {
            return true;
        }

        $blockerBlocksLoaded = $this->relationLoaded('blockerBlocks');
        if ($blockerBlocksLoaded) {
            $this->unsetRelation('blockerBlocks');
        }

        return (bool) $this->blockedItems(\get_class($object))
            ->detach($object->getKey());
    }

    protected function blockedItems(string $class): MorphToMany
    {
        return $this->morphedByMany(
            $class,
            'blockable',
            config('block.models.pivot'),
            config('block.column_names.user_foreign_key')
        )
            ->withTimestamps();
    }
}
