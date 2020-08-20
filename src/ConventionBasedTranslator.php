<?php

namespace Core\Command;

use Core\Contracts\Command;
use Core\Contracts\CommandTranslator;
use Exception;

class ConventionBasedTranslator implements CommandTranslator
{

    /**
     * Translate command class name to handler class name.
     * @param Command $command
     * @param string $namespace
     * @param string $suffix
     * @return string
     * @throws Exception
     */
    public function translate(Command $command, $namespace = 'Handlers', $suffix = 'Handler')
    {
        $namespaced_command_class = get_class($command);
        $parts = explode('\\', $namespaced_command_class);
        $parts_count = count($parts);

        if ($parts_count > 1) {
            // from: Services\User\Commands\RegisterUser
            // to: Services\User\Handlers\RegisterUserHandler
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
            // from: RegisterUser
            // to: RegisterUserHandler
            $handler_class = $namespaced_command_class . $suffix;
        }

        if (class_exists($handler_class)) {
            return $handler_class;
        }
        throw new HandlerResolutionException($handler_class);
    }
}