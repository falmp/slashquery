<?php
/**
 * cPanel - /slashquery/core/modules/configuration/commands/class.cache.php
 *
 * get Cache stats
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqCache extends sqFinalCommand {

  public function run() {

    /**
     * start the template
     */
    $tpl = $this->TPL();

		if ($uid = sqSession::Get('uid')) {
      $tpl['uid'] = $uid;
      $tpl['js'] = array('vendor/jquery.blockUI.js', 'cflushCache.js');

      $tpl['command_tpl'] = 'cache.tpl';

      $tpl['caches'] = array('Site' => $tpl->_cache()->stats(), 'Database' => $this->DB()->Cache()->stats());

      $this->notify();
    }

  }

}
