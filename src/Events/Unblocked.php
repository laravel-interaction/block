<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Events;

use Illuminate\Database\Eloquent\Model;

class Unblocked
{
    public function __construct(
        public Model $model
    ) {
    }
}
