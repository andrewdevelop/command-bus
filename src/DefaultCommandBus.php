<?php

namespace Core\Command;

use Core\Contracts\Command;
use Core\Contracts\CommandBus as Contract;
use Core\Contracts\CommandHandler;
use Core\Contracts\CommandTranslator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class DefaultCommandBus implements Contract
{
    /**
     * DI Container.
     */
    private ContainerInterface $container;

    /**
     * Class translator.
     */
    private CommandTranslator $translator;

    /**
     * Bus constructor.
     * @param ContainerInterface $container
     * @param CommandTranslator $translator
     */
    public function __construct(ContainerInterface $container, CommandTranslator $translator)
    {
        $this->container = $container;
        $this->translator = $translator;
    }

    /**
     * Execute a command/query.
     * @param Command $command
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws HandlerResolutionException
     */
    public function execute(Command $command)
    {
        $handler = $this->resolveCommandHandler($command);
        return $handler->handle($command);
    }

    /**
     * Make a command handler instance.
     * @param Command $command
     * @return CommandHandler
     * @throws ContainerExceptionInterface
     * @throws HandlerResolutionException
     */
    protected function resolveCommandHandler(Command $command): CommandHandler
    {
        $class = $this->translator->translate($command);
        return $this->container->get($class);
    }
}