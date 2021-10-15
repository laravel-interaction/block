<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests\Configuration;

use LaravelInteraction\Block\Block;
use LaravelInteraction\Block\Tests\Models\Channel;
use LaravelInteraction\Block\Tests\Models\User;
use LaravelInteraction\Block\Tests\TestCase;

/**
 * @internal
 */
final class UuidsTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        config([
            'block.uuids' => true,
        ]);
    }

    public function testKeyType(): void
    {
        $block = new Block();
        self::assertSame('string', $block->getKeyType());
    }

    public function testIncrementing(): void
    {
        $block = new Block();
        self::assertFalse($block->getIncrementing());
    }

    public function testKeyName(): void
    {
        $block = new Block();
        self::assertSame('uuid', $block->getKeyName());
    }

    public function testKey(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->block($channel);
        self::assertIsString($user->blockerBlocks()->firstOrFail()->getKey());
    }
}
