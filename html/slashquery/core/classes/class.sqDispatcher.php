<?php
/**
 * sqDispatcher - /slashquery/core/classes/class.sqDispatcher.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqDispatcher extends sqSubject {
  /**
   * sqSite object called by sqView
   */
  public $SQ;

  /**
   * dispatch the site
   *
   * @param sqSite $SQ
   */
  public function Dispatch(sqSite $SQ) {

    $this->SQ = $SQ;

    switch (true) {
      /**
       * Cpanel
       */
      case $SQ->router->isCpanel:
        /**
         * if permissions (true) dispatch else showerrror
         */
        $SQ->ACL()->check();
        if (!$SQ->DB()->CachePGetOne(86400, 'SELECT IF(status, 1, 0) FROM sq_modules WHERE cpanel > 0 AND name=? ', $SQ->router->module, 'group:ACL')) {
          $SQ->TPL()->showError = 1;
          $this->notify();
        } else {
          /**
           * dispatch
           */
          if ($SQ->router->isCpanelExt) {
            require_once SQ_ROOT . 'slashquery/sites/' . $SQ->router->site . '/cpanel/' . $SQ->router->module . '/' . $SQ->router->module . '.php';
          } else {
            require_once SQ_ROOT . 'slashquery/core/modules/' . $SQ->router->module .'/' . $SQ->router->module . '.php';
          }
        }
        break;

      /**
       * xx1032 - common between cpanel & modules
       */
      case $SQ->router->xx1032:
        require_once SQ_ROOT . 'slashquery/xx-10-32/switch.php';
        break;

      /**
       * default
       */
      default :
        /**
         * dispatcher hook
         */
        $hook = SITE_ROOT . '/hook.php';
        if (file_exists($hook)) {
          require_once $hook;
        }

        /**
         * functions for the site
         */
        if (file_exists(SITE_ROOT . '/include/functions.php')) {
          require_once SITE_ROOT . '/include/functions.php';
        }

        /**
         * Add the classes and module path to the Autoloader
         */
        sqAutoLoader::addPath(SQ_ROOT . 'slashquery/sites/' . $SQ->router->site . '/classes');
        sqAutoLoader::addPath(SQ_ROOT . 'slashquery/sites/' . $SQ->router->site . '/modules/'.$SQ->router->module);

        /**
         * dispatch the query
         */
        require_once SQ_ROOT . 'slashquery/sites/' . $SQ->router->site . '/modules/' . $SQ->router->module . '/' . $SQ->router->module . '.php';
    }

  }

  /**
   * checkPerms
   *
   * verify that the current session has permissions to access the module
   */
  public function checkPerms($perm=null) {
    sqSession::Start();

    if (!$this->SQ->ACL()->check($perm)) {
      /**
       * 0 = accessDenied.tpl
       * 1 = moduleDisabled.tpl
       */
      $this->SQ->TPL()->showError = 0;
      $this->notify();
      exit;
    }

  }

}
