# fluffy

Fluffy framework. Based on Viewi and Swoole.

Not ready for production.

## Commands

### Set up Nginx on WSL

`php fluffy nginx nutrition.wsl.com`

### Run server

`php fluffy server`

### Reload server

`php fluffy reload`

### Watch mode - runs server and rebuilds application on file changes

`php fluffy watch`

### Build - builds the app

`php fluffy build [environment]`

For example:

`php fluffy build dev`

`php fluffy build prod`

`php fluffy build local`

### Install (create all tables)

`php fluffy install`

### Run migrations

`php fluffy migrate`

Rollback migration

`php fluffy migrate rollback Application\\Migrations\\Menu\\MenuItemMigration`

### Create Entity model

`php fluffy model create EntityName [Namespace]`

Example:

`php fluffy model create UserTokenEntity Auth`

### Generate Entity model repository, migration, service; register migration, repository, service

`php fluffy model build UserTokenEntity Auth`

### Generate Controller

`Catalog/ProductController`

`php fluffy controller create Product Catalog`

### Cron Tab

`Application\crontab.php`

```php
CronTab::schedule([TestTask::class, 'execute'], '*/5 * * * * *');
```

### Hubs (Web sockets)

`Application\hubs.php`

```php
Hubs::mapHub('collect', [CollectHub::class, 'collect']);
```

```php
<?php

namespace Application\Hubs;

use Application\Models\CollectModel;

class CollectHub
{
    public function collect(CollectModel $message, $data, string $name)
    {
        print_r(['CollectHub::collect', $message->date, $data, $name]);
    }
}
```

```js
websocket.send(JSON.stringify({ 
  route: 'collect',
  data: { 
    name: 'Viewi',
    date: 123
  }
}));
```

## Controllers

Optionally, BaseController provides default response methods

`use Fluffy\Controllers\BaseController`

```php
<?php

namespace Application\Controllers;

use Fluffy\Controllers\BaseController;

class TestController extends BaseController
{
}
```
