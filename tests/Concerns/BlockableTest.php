<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests\Concerns;

use LaravelInteraction\Block\Tests\Models\Channel;
use LaravelInteraction\Block\Tests\Models\User;
use LaravelInteraction\Block\Tests\TestCase;

class BlockableTest extends TestCase
{
    public function modelClasses(): array
    {
        return[[Channel::class], [User::class]];
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel $modelClass
     */
    public function testBlocks($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame(1, $model->blockableBlocks()->count());
        self::assertSame(1, $model->blockableBlocks->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel $modelClass
     */
    public function testBlockersCount($modelClass): void
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
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel $modelClass
     */
    public function testBlockersCountForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame('1', $model->blockersCountForHumans());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel $modelClass
     */
    public function testIsBlockedBy($modelClass): void
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
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel $modelClass
     */
    public function testIsNotBlockedBy($modelClass): void
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
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel $modelClass
     */
    public function testBlockers($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame(1, $model->blockers()->count());
        $user->unblock($model);
        self::assertSame(0, $model->blockers()->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel $modelClass
     */
    public function testScopeWhereBlockedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->block($model);
        self::assertSame(1, $modelClass::query()->whereBlockedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereBlockedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Block\Tests\Models\User|\LaravelInteraction\Block\Tests\Models\Channel $modelClass
     */
    public function testScopeWhereNotBlockedBy($modelClass): void
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
