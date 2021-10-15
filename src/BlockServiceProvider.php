<?php

declare(strict_types=1);

namespace LaravelInteraction\Block;

use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\InteractionServiceProvider;

class BlockServiceProvider extends InteractionServiceProvider
{
    /**
     * @var string
     */
    protected $interaction = InteractionList::BLOCK;
}
