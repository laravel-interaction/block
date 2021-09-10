<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Block\Events\Blocked;
use LaravelInteraction\Block\Tests\Models\Channel;
use LaravelInteraction\Block\Tests\Models\User;
use LaravelInteraction\Block\Tests\TestCase;

class BlockedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Blocked::class]);
        $user->block($channel);
        Event::assertDispatchedTimes(Blocked::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Blocked::class]);
        $user->block($channel);
        $user->block($channel);
        $user->block($channel);
        Event::assertDispatchedTimes(Blocked::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Blocked::class]);
        $user->toggleBlock($channel);
        Event::assertDispatchedTimes(Blocked::class);
    }
}
