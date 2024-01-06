# fluffy

Fluffy framework. Based on Viewi and Swoole.

Not ready for production.

## Commands

### Run server

`bin/start server`

### Reload server

`bin/start reload`

### Watch mode - runs server and rebuilds application on file changes

`bin/start watch`

### Build - builds the app

`bin/start build [environment]`

For example:

`bin/start build dev`

`bin/start build prod`

`bin/start build local`

### Install (create all tables)

`bin/start install`

### Run migrations

`bin/start migrate`

### Create Entity model

`bin/start model create EntityName [Namespace]`

Example:

`bin/start model create UserTokenEntity Auth`

### Generate Entity model repository, migration, service; register migration, repository, service

`bin/start model build UserTokenEntity Auth`

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
