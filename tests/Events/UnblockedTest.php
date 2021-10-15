<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Block\Events\Unblocked;
use LaravelInteraction\Block\Tests\Models\Channel;
use LaravelInteraction\Block\Tests\Models\User;
use LaravelInteraction\Block\Tests\TestCase;

/**
 * @internal
 */
final class UnblockedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->block($channel);
        Event::fake([Unblocked::class]);
        $user->unblock($channel);
        Event::assertDispatchedTimes(Unblocked::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->block($channel);
        Event::fake([Unblocked::class]);
        $user->unblock($channel);
        $user->unblock($channel);
        Event::assertDispatchedTimes(Unblocked::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Unblocked::class]);
        $user->toggleBlock($channel);
        $user->toggleBlock($channel);
        Event::assertDispatchedTimes(Unblocked::class);
    }
}
