<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests\Concerns;

use Iterator;
use LaravelInteraction\Block\Tests\Models\Channel;
use LaravelInteraction\Block\Tests\Models\User;
use LaravelInteraction\Block\Tests\TestCase;

/**
 * @internal
 */
final class BlockableTest extends TestCase
{
    /**
     * @return \Iterator<array<class-string<\LaravelInteraction\Block\Tests\Models\Channel|\LaravelInteraction\Block\Tests\Models\User>>>
     */
    public function provideModelClasses(): Iterator
    {
        yield [Channel::class];

        yield [User::class];
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel> $modelClass
     */
    public function testBlocks(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame(1, $model->blockableBlocks()->count());
        self::assertSame(1, $model->blockableBlocks->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel> $modelClass
     */
    public function testBlockersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame(1, $model->blockersCount());
        $user->unblock($model);
        self::assertSame(1, $model->blockersCount());
        $model->loadCount('blockers');
        self::assertSame(0, $model->blockersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel> $modelClass
     */
    public function testBlockersCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame('1', $model->blockersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel> $modelClass
     */
    public function testIsBlockedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isBlockedBy($model));
        $user->block($model);
        self::assertTrue($model->isBlockedBy($user));
        $model->load('blockers');
        $user->unblock($model);
        self::assertTrue($model->isBlockedBy($user));
        $model->load('blockers');
        self::assertFalse($model->isBlockedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel> $modelClass
     */
    public function testIsNotBlockedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotBlockedBy($model));
        $user->block($model);
        self::assertFalse($model->isNotBlockedBy($user));
        $model->load('blockers');
        $user->unblock($model);
        self::assertFalse($model->isNotBlockedBy($user));
        $model->load('blockers');
        self::assertTrue($model->isNotBlockedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel> $modelClass
     */
    public function testBlockers(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame(1, $model->blockers()->count());
        $user->unblock($model);
        self::assertSame(0, $model->blockers()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereBlockedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame(1, $modelClass::query()->whereBlockedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereBlockedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotBlockedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotBlockedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotBlockedBy($other)->count());
    }
}
