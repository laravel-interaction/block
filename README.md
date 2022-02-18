# Laravel Block

User block/unblock behaviour for Laravel.

<p align="center">
<a href="https://packagist.org/packages/laravel-interaction/block"><img src="https://poser.pugx.org/laravel-interaction/block/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/block"><img src="https://poser.pugx.org/laravel-interaction/block/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel-interaction/block"><img src="https://poser.pugx.org/laravel-interaction/block/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/block"><img src="https://poser.pugx.org/laravel-interaction/block/license" alt="License"></a>
</p>

> **Requires [PHP 7.3+](https://php.net/releases/)**

Require Laravel Block using [Composer](https://getcomposer.org):

```bash
composer require laravel-interaction/block
```

## Usage

### Setup Blocker

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Block\Concerns\Blocker;

class User extends Model
{
    use Blocker;
}
```

### Setup Blockable

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Block\Concerns\Blockable;

class Channel extends Model
{
    use Blockable;
}
```

### Blocker

```php
use LaravelInteraction\Block\Tests\Models\Channel;
/** @var \LaravelInteraction\Block\Tests\Models\User $user */
/** @var \LaravelInteraction\Block\Tests\Models\Channel $channel */
// Block to Blockable
$user->block($channel);
$user->unblock($channel);
$user->toggleBlock($channel);

// Compare Blockable
$user->hasBlocked($channel);
$user->hasNotBlocked($channel);

// Get blocked info
$user->blockerBlocks()->count(); 

// with type
$user->blockerBlocks()->withType(Channel::class)->count(); 

// get blocked channels
Channel::query()->whereBlockedBy($user)->get();

// get blocked channels doesnt blocked
Channel::query()->whereNotBlockedBy($user)->get();
```

### Blockable

```php
use LaravelInteraction\Block\Tests\Models\User;
use LaravelInteraction\Block\Tests\Models\Channel;
/** @var \LaravelInteraction\Block\Tests\Models\User $user */
/** @var \LaravelInteraction\Block\Tests\Models\Channel $channel */
// Compare Blocker
$channel->isBlockedBy($user); 
$channel->isNotBlockedBy($user);
// Get blockers info
$channel->blockers->each(function (User $user){
    echo $user->getKey();
});

$channels = Channel::query()->withCount('blockers')->get();
$channels->each(function (Channel $channel){
    echo $channel->blockers()->count(); // 1100
    echo $channel->blockers_count; // "1100"
    echo $channel->blockersCount(); // 1100
    echo $channel->blockersCountForHumans(); // "1.1K"
});
```

### Events

| Event | Fired |
| --- | --- |
| `LaravelInteraction\Block\Events\Blocked` | When an object get blocked. |
| `LaravelInteraction\Block\Events\Unblocked` | When an object get unblocked. |

## License

Laravel Block is an open-sourced software licensed under the [MIT license](LICENSE).
