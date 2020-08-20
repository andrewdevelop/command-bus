<?php

namespace Core\Command;

use Core\Contracts\Command;
use Core\Contracts\CommandBus as Contract;
use Core\Contracts\CommandHandler;
use Core\Contracts\CommandTranslator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;

class DefaultCommandBus implements Contract
{
    /**
     * DI Container.
     * @var Container
     */
    private $container;

    /**
     * Class translator.
     * @var CommandTranslator
     */
    private $translator;

    /**
     * List of middlewares.
     * @var array
     */
    private $middlewares = [];

    /**
     * Cached middleware instances.
     * @var array
     */
    private $cache = [];

    /**
     * Bus constructor.
     * @param Container $container
     * @param CommandTranslator $translator
     */
    public function __construct(Container $container, CommandTranslator $translator)
    {
        $this->container = $container;
        $this->translator = $translator;
    }

    /**
     * Execute a command.
     * @param Command $command
     * @return mixed
     * @throws BindingResolutionException
     */
    public function execute(Command $command)
    {
        foreach ($this->middlewares as $class) {
            $middleware = $this->resolveMiddleware($class);
            $command = $middleware->handle($command);
        }
        $handler = $this->resolveCommandHandler($command);
        return $handler->handle($command);
    }

    /**
     * Add a middleware.
     * @param $middleware
     */
    public function middleware($middleware)
    {
        if (!is_array($middleware)) $middleware = [$middleware];
        $this->middlewares = array_merge($this->middlewares, $middleware);
    }

    /**
     * Make a command handler instance.
     * @param Command $command
     * @return CommandHandler
     * @throws BindingResolutionException
     */
    protected function resolveCommandHandler(Command $command)
    {
        $class = $this->translator->translate($command);
        return $this->container->make($class);
    }

    /**
     * Create or make a middleware instance.
     * @param string $class
     * @return Middleware
     * @throws BindingResolutionException
     */
    protected function resolveMiddleware(string $class)
    {
        if (isset($this->cache[$class])) return $this->cache[$class];
        $instance = $this->container->make($class);
        $this->cache[$class] = $instance;
        return $instance;
    }
}