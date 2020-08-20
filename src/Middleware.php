<?php

namespace Core\Command;

use Core\Contracts\Command;

interface Middleware
{
    /**
     * @param Command $command
     * @return Command
     */
    public function handle(Command $command);
}