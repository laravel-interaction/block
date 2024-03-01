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
    private User $user;

    private Channel $channel;

    private Block $block;

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
        $this->assertInstanceOf(Carbon::class, $this->block->created_at);
        $this->assertInstanceOf(Carbon::class, $this->block->updated_at);
    }

    public function testScopeWithType(): void
    {
        $this->assertSame(1, Block::query()->withType(Channel::class)->count());
        $this->assertSame(0, Block::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        $this->assertSame(config('block.table_names.pivot'), $this->block->getTable());
    }

    public function testBlocker(): void
    {
        $this->assertInstanceOf(User::class, $this->block->blocker);
    }

    public function testBlockable(): void
    {
        $this->assertInstanceOf(Channel::class, $this->block->blockable);
    }

    public function testUser(): void
    {
        $this->assertInstanceOf(User::class, $this->block->user);
    }

    public function testIsBlockedTo(): void
    {
        $this->assertTrue($this->block->isBlockedTo($this->channel));
        $this->assertFalse($this->block->isBlockedTo($this->user));
    }

    public function testIsBlockedBy(): void
    {
        $this->assertFalse($this->block->isBlockedBy($this->channel));
        $this->assertTrue($this->block->isBlockedBy($this->user));
    }
}
