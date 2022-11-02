<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Block\Block;
use LaravelInteraction\Block\Tests\Models\Channel;
use LaravelInteraction\Block\Tests\Models\User;

/**
 * @internal
 */
final class BlockTest extends TestCase
{
    private \LaravelInteraction\Block\Tests\Models\User $user;

    private \LaravelInteraction\Block\Tests\Models\Channel $channel;

    private \LaravelInteraction\Block\Block $block;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->block($this->channel);
        $this->block = Block::query()->firstOrFail();
    }

    public function testBlockTimestamp(): void
    {
        self::assertInstanceOf(Carbon::class, $this->block->created_at);
        self::assertInstanceOf(Carbon::class, $this->block->updated_at);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Block::query()->withType(Channel::class)->count());
        self::assertSame(0, Block::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        self::assertSame(config('block.table_names.pivot'), $this->block->getTable());
    }

    public function testBlocker(): void
    {
        self::assertInstanceOf(User::class, $this->block->blocker);
    }

    public function testBlockable(): void
    {
        self::assertInstanceOf(Channel::class, $this->block->blockable);
    }

    public function testUser(): void
    {
        self::assertInstanceOf(User::class, $this->block->user);
    }

    public function testIsBlockedTo(): void
    {
        self::assertTrue($this->block->isBlockedTo($this->channel));
        self::assertFalse($this->block->isBlockedTo($this->user));
    }

    public function testIsBlockedBy(): void
    {
        self::assertFalse($this->block->isBlockedBy($this->channel));
        self::assertTrue($this->block->isBlockedBy($this->user));
    }
}
