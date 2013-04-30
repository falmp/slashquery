<?php
/**
 * cPanel - /slashquery/sites/slashquery/core/modules/configuration/rules/class.flushCache.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqFlushCache extends sqFinalRule {

  protected function evaluate() {

    if (extension_loaded('apc') && ini_get('apc.enabled')) {
			apc_clear_cache();
		  apc_clear_cache('user');
		}

    /**
     * flush template cache
     */
    $this->SQ->TPL()->_cache()->flush();

    /**
     * flush database template
     */
		$this->DB()->CacheFlush();

    sqTools::jStatus(true);
  }

}
