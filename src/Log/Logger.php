<?php

namespace Leaf\Log;

use Leaf\Application;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class Logger extends \Monolog\Logger
{
    public function __construct($config = array())
    {
        $app = Application::$app;
        $config = $config + [
                'name' => $app['name'], //channel
                'level' => $app['debug'] ? Logger::DEBUG : Logger::INFO
                //handlers => [],
                //processors => [],
            ];

        $logPath = $app->getRuntimePath() . '/logs/';
        $filename = $config['name'] . '.log';

        $formatter = new Formatter();
        //$formatter = new LineFormatter();
        //$formatter->includeStacktraces();

        $handler = new RotatingFileHandler($logPath . $filename, 30, $config['level']);
        $handler->setFormatter($formatter);

        $config = $config + [
                'handlers' => array($handler),
                'processors' => array(
                    new \Monolog\Processor\WebProcessor(),
                    new \Monolog\Processor\IntrospectionProcessor($config['level'], ['Leaf\\Facade\\LogFacade'])
                ),
            ];

        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }

    }
}