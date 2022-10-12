<?php

namespace Bixie\DfmApi\Helpers;

use Bixie\DfmApi\Lime\Helper;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger as MonoLogger;
use Monolog\Processor\WebProcessor;

class Logger extends Helper {

    protected MonoLogger $logger;

    public function initialize ()
    {
        $this->logger = (new MonoLogger('dfm-api'))
            ->setTimezone(new \DateTimeZone('Europe/Amsterdam'))
            ->pushHandler(
                (new RotatingFileHandler(
                $this->app['path.logs'] . '/dfm-api', 12, Level::Info
                 ))->setFilenameFormat('{filename}-{date}', RotatingFileHandler::FILE_PER_MONTH)
            )
            ->pushProcessor(new WebProcessor());
    }

    public function __call($method, $parameters)
    {
        $this->logger->{$method}(...$parameters);

        return $this->logger;
    }

}
