<?php

namespace Core\Command;

use Core\Contracts\Command;
use Core\Contracts\CommandTranslator;

class ConventionBasedTranslator implements CommandTranslator
{

    /**
     * Translate command class name to handler class name.
     * @param Command $command
     * @param string $namespace
     * @param string $suffix
     * @return string
     * @throws HandlerResolutionException
     */
    public function translate(
        Command $command,
        string $namespace = 'Handlers',
        string $suffix = 'Handler'
    ): string
    {
        $namespaced_command_class = get_class($command);
        $parts = explode('\\', $namespaced_command_class);
        $parts_count = count($parts);
        if ($parts_count > 1) {
            // e.g. Services\User\Commands\RegisterUser -> Services\User\Handlers\RegisterUserHandler
            for ($i = 0; $i < $parts_count; $i++) {
                if ($i == $parts_count - 2) {
                    $parts[$i] = $namespace;
                }
                if ($i == $parts_count - 1) {
                    $parts[$i] = $parts[$i] . $suffix;
                }
            }
            $handler_class = implode('\\', $parts);
        } else {
            // e.g. RegisterUser -> RegisterUserHandler
            $handler_class = $namespaced_command_class . $suffix;
        }

        if (class_exists($handler_class)) {
            return $handler_class;
        }

        throw new HandlerResolutionException($handler_class);
    }
}