<?php
/**
 * Front Controller - /index.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

/**
 * @var float for measuring the site
 */
$time = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);

/**
 * define the document root for SlashQuery
 */
define('SQ_ROOT', __DIR__ . '/');

/**
 * SQ configuration
 */
require_once SQ_ROOT . 'slashquery/config.php';

/**
 * error_handler
 */
require_once SQ_ROOT . 'slashquery/core/include/error_handler.php';

/**
 * Autoload
 */
require_once SQ_ROOT . 'slashquery/core/classes/class.sqAutoload.php';

/**
 * start the loger if $_GET['debug'] exists
 */
sqLogger::init($time, ( isset($_GET['debug']) || isset($_SERVER['SQ_ENV']) ) );

/**
 * Initialize the dispatcher
 */
$run = new sqDispatcher();

/**
 * Attach the view object (observer / template of the site)
 *
 * @param sqView object
 */
$run->attach(new sqView());

/**
 * dispatch the site
 *
 * @param sqSite object
 */
$run->dispatch(new sqSite( new sqRouter( sqSites::getSite($time) ) ));

sqLogger::End();
