<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests\Concerns;

use LaravelInteraction\Block\Block;
use LaravelInteraction\Block\Tests\Models\Channel;
use LaravelInteraction\Block\Tests\Models\User;
use LaravelInteraction\Block\Tests\TestCase;

/**
 * @internal
 */
final class BlockerTest extends TestCase
{
    public function testBlock(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->block($channel);
        $this->assertDatabaseHas(
            Block::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'blockable_type' => $channel->getMorphClass(),
                'blockable_id' => $channel->getKey(),
            ]
        );
        $user->load('blockerBlocks');
        $user->unblock($channel);
        $user->load('blockerBlocks');
        $user->block($channel);
    }

    public function testUnblock(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->block($channel);
        $this->assertDatabaseHas(
            Block::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'blockable_type' => $channel->getMorphClass(),
                'blockable_id' => $channel->getKey(),
            ]
        );
        $user->unblock($channel);
        $this->assertDatabaseMissing(
            Block::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'blockable_type' => $channel->getMorphClass(),
                'blockable_id' => $channel->getKey(),
            ]
        );
    }

    public function testToggleBlock(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBlock($channel);
        $this->assertDatabaseHas(
            Block::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'blockable_type' => $channel->getMorphClass(),
                'blockable_id' => $channel->getKey(),
            ]
        );
        $user->toggleBlock($channel);
        $this->assertDatabaseMissing(
            Block::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'blockable_type' => $channel->getMorphClass(),
                'blockable_id' => $channel->getKey(),
            ]
        );
    }

    public function testBlocks(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBlock($channel);
        self::assertSame(1, $user->blockerBlocks()->count());
        self::assertSame(1, $user->blockerBlocks->count());
    }

    public function testHasBlocked(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBlock($channel);
        self::assertTrue($user->hasBlocked($channel));
        $user->toggleBlock($channel);
        $user->load('blockerBlocks');
        self::assertFalse($user->hasBlocked($channel));
    }

    public function testHasNotBlocked(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBlock($channel);
        self::assertFalse($user->hasNotBlocked($channel));
        $user->toggleBlock($channel);
        self::assertTrue($user->hasNotBlocked($channel));
    }
}
