<?php

namespace App\Logging;

class Customize
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        dd('Customize logger');
        // foreach ($logger->getHandlers() as $handler) {
        //     $handler->setFormatter();
        // }
    }
}
