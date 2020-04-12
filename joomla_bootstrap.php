<?php

// We are a valid entry point.
const _JEXEC = 1;
$app_config = include __DIR__ . '/config.php';

define('JOOMLA_ROOT', realpath(__DIR__ . $app_config['joomla_root']));

// Load system defines
if (file_exists(JOOMLA_ROOT . '/defines.php'))
{
    require_once JOOMLA_ROOT . '/defines.php';
}

if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', JOOMLA_ROOT);
    require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

// System configuration.
$config = new JConfig;
define('JDEBUG', $config->debug);
