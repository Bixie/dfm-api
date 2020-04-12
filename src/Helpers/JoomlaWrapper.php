<?php

namespace Bixie\DfmApi\Helpers;

use Lime\Helper;

class JoomlaWrapper extends Helper {

    protected $dispatcher;

    public function initialize ()
    {
        include realpath(__DIR__ . '/../../joomla_bootstrap.php');
        //trick joomla
        define('JPATH_COMPONENT', 'com_content');
        $_SERVER['HTTP_HOST'] = 'domain.com';
        \JFactory::getApplication('site');
        // Import the plugins.
        \JPluginHelper::importPlugin('system');
        //get dispatcher
        $this->dispatcher = \JDispatcher::getInstance();
    }

    public function trigger (string $eventName, array $args)
    {
        return $this->dispatcher->trigger($eventName, $args);
    }

}
