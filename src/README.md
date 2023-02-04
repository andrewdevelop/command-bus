# andrewdevelop/command-bus
This is a command bus library for Lumen or Laravel 9+. 
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

### PS
If the package was helpful, don't forget to buy a coffee for the developer.  Thx.  

<a href='https://ko-fi.com/andrewdevelop' target='_blank'>
  <img height='36' style='border:0px;height:36px;' src='https://az743702.vo.msecnd.net/cdn/kofi3.png?v=2' border='0' alt='Buy Me a Coffee at ko-fi.com' />
</a>
