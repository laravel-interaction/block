<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Block\Concerns\Blockable;
use LaravelInteraction\Block\Concerns\Blocker;

/**
 * @method static \LaravelInteraction\Block\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Blockable;
    use Blocker;
}
