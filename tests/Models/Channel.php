<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Block\Concerns\Blockable;

/**
 * @method static \LaravelInteraction\Block\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Blockable;
}
