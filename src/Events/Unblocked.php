<?php

declare(strict_types=1);

namespace LaravelInteraction\Block\Events;

use Illuminate\Database\Eloquent\Model;

class Unblocked
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $block;

    public function __construct(Model $block)
    {
        $this->block = $block;
    }
}
