<?php

declare(strict_types=1);

namespace LaravelInteraction\Block;

use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\InteractionServiceProvider;

class BlockServiceProvider extends InteractionServiceProvider
{
    protected $interaction = InteractionList::BLOCK;
}
