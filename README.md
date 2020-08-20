# andrewdevelop/command-bus
This is a command bus library for Lumen or Laravel 6+. 
The package provides infrastructure to execute reusable commands in your application: HTTP controllers, CLI, queues, etc., also it makes your code more readable and well organized.
The service executes the required Command using in a Command Handler using a simple naming convention.

## Installation
Package is available on Packagist. To install, just run:
```text
composer require andrewdevelop/command-bus
```

## Setup
Simply add this to your `/bootstrap/app.php` file:
```php
$app->register(Core\Command\CommandBusServiceProvider::class);
```

## Usage
Consider using the command bus in a basic controller:
```php
<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller; 
use Core\Contracts\CommandBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Posts\Commands\CreatePostCommand;

class PostController extends Controller
{
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }
    
    public function store(Request $request)
    {
        $command = new CreatePostCommand($request->get('title'), $request->get('content'));
        $result = $this->bus->execute($command);
        if ($result) {
            return new JsonResponse($result, 201);
        }
        return new JsonResponse(['error' => 'Invalid request'], 400);
    }
}
```
Let's create a basic command:
```php
<?php

namespace App\Posts\Commands;

use Core\Contracts\Command;

class CreateAccountCommand implements Command
{
    public $title;  

    public $content;

    public function __construct(?string $title, ?string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }
}
```

Let's create a handler associated with command.
Command handlers supports constructor dependency injection using Laravel's service container.
```php
<?php

namespace App\Posts\Handlers;

class CreateAccountCommandHandler implements CommandHandler
{
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Command $command)
    {
        $post = $this->repository->init(PostAggregateRoot::class);
        $post->create($command->title, $command->content);
        return $this->repository->save($post);
    }
}
```

## Using middlewares
To register additional middlewares globally we can do something like:
```php
<?php

// Somewhere in a service provider, or in /bootstrap/app.php
$this->app->resolving(\Core\Contracts\CommandBus::class, function ($bus, $app) {
    $bus->middleware([
        FixEmptyInputMiddleware::class,
        ValidateCommandMiddleware::class,
        // And a lot of things that are limited only by your imagination.
    ]);
});
```

Your handler must implement the `Core\Command\Middleware` interface and return an instance of `Core\Contracts\Command`.
Middlewares will be applied before the command execution.
```php
<?php

use Core\Command\Middleware;
use Core\Contracts\Command;

class SomeMiddleware implements Middleware 
{
    public function handle(Command $command)
    {
        // Any code that can modify, validate, log or anything with the command.
        // And don't forget to return the command.
        return $command;
    }
}
``` 

### PS
If the package was helpful, don't forget to buy a coffee for the developer. 
<a href='https://ko-fi.com/andrewdevelop' target='_blank'>
  <img height='36' style='border:0px;height:36px;' src='https://az743702.vo.msecnd.net/cdn/kofi3.png?v=2' border='0' alt='Buy Me a Coffee at ko-fi.com' />
</a>