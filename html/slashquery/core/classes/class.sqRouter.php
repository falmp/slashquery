<?php
/**
 * sqRouter - /slashquery/core/classes/class.sqRouter.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqRouter extends sqBase {

  /**
   * Start the router - /module/command/args
   *
   * @param string $site
   */
  public function __construct($site) {
    $this->site = $site;
    $this->isCpanel = false;
    $this->isCpanelExt = false;

    /**
     * split the URI using / and ? as separator
     */
    $uri = preg_split('#[/?]#', urldecode(strip_tags($_SERVER['REQUEST_URI'])), -1, PREG_SPLIT_NO_EMPTY);

    /**
     * set host (everything before a .) in domain
     */
    $this->host = preg_match('#^(\w[^\.]+)#', $_SERVER['HTTP_HOST'], $matches) ? current($matches) : null;

    /**
     * check if host is cpanel
     */
    $this->query = preg_match('#^cpanel\.#i', $_SERVER['HTTP_HOST']) ? 'cpanel' : array_shift($uri);

    /**
     * check if is an AJAX request
     */
    $this->ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    sqLogger::log(__CLASS__, '/? ' . $this->query);

    /**
     * crete the rout for the $uri
     */
    $this->route($uri);
  }

  /**
   * route the URI
   *
   * @access protected
   * @param string $uri
   */
  protected function route($uri) {

    switch ($this->query) {
      case 'cpanel':
        /**
         * session start & check the global UID for avoiding login twice
         */
        sqSession::Start();
        $GLOBALS['UID'] = sqSession::Get('uid') ?  : null;

        $module = array_shift($uri);

        /**
         * set module to 'cpanel'
         * this changes depending if site contain extensions or not
         */
        $this->module = 'cpanel';
        $this->command = $module;

        /**
         * search if module is one of the defaults core modules
         */
        if (in_array($module, array('users', 'ACL', 'configuration'))) {
          $this->module = $module;
          $this->command = array_shift($uri);
        } else {
          /**
           * check for global extensions
           */
          $cPanel_core = SQ_ROOT . 'slashquery/core/modules/';
          if (in_array($module, array_diff(scandir($cPanel_core), array('.','..')))) {
            $this->module = $module;
            $this->command = array_shift($uri);
          } else {
            /**
             * check for extensions on site
             */
            $cPanel_ext = SQ_ROOT . 'slashquery/sites/' . $this->site . '/cpanel';
            if (is_dir($cPanel_ext)) {
              if (in_array($module, array_diff(scandir($cPanel_ext), array('.','..')))) {
                $this->module = $module;
                $this->command = array_shift($uri);
                $this->isCpanelExt = true;
              }
            }
          }
        }

        $this->isCpanel = true;
        break;

      /**
       * check for any pattern  xx-10-32 - chars-expiry-captcha
       */
      case (preg_match('#^[\w]{2}-10-32$#', $this->query) ? $this->query : !$this->query) :
      case (preg_match('#^[\w]{2}-\d{6,10}-[a-fA-F0-9]{32}$#', $this->query) ? $this->query : !$this->query) :
        $this->xx1032 = true;
        break;

      /**
       * process normal slash queries /?...
       */
      default :
        /**
         * sanitize module, default to 'main'
         */
        $module = function ($c) {
          return $c ? (preg_match('/^[a-zA-Z0-9-_\.]{0,40}+$/', $c) ? $c : 'main') : 'main';
        };

        if (is_dir(SQ_ROOT . 'slashquery/sites/' . $this->site . '/modules/' . $module($this->query))) {
            $this->module = $module($this->query);
            $this->command = array_shift($uri);
        } else {
            $this->module = 'main';
            $this->command = $this->query;
        }
    }

    /**
     * URI - Type Juggling
     */
    if (is_array($uri)) {
      foreach ($uri as $key => $arg) {
        if (is_numeric($arg)) {
          $uri[$key] = !strcmp(intval($arg), $arg) ? (int) $arg : (!strcmp(floatval($arg), $arg) ? (float) $arg : $arg);
        }
      }
    } else {
      $uri = !strcmp(intval($uri), $uri) ? (int) $uri : (!strcmp(floatval($uri), $uri) ? (float) $uri : $uri);
    }

    $this->arguments = $uri;
    sqLogger::log(__METHOD__, "$this->site -> module: $this->module / command: $this->command / arguments:", $this->arguments);
  }

}
