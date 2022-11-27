<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Events;

use Illuminate\Database\Eloquent\Model;

class Blocked
{
    public function __construct(
        public Model $model
    ) {
    }
}
