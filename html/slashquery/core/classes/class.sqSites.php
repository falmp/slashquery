<?php
/**
 * sqSites - /slashquery/core/init/class.sqSites.php
 * extract the site from the $_SERVER['HTTP_HOST'] and search site on the
 * sites.php array
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqSites {
  private static $sqSite = null;

	/**
	 * getSite
	 *
	 * @return string site path
	 */
	public static function getSite() {
    $sqSites = new sqArray();
		include_once SQ_ROOT . 'slashquery/sites.php';

		$findSite = function($sites) {
			foreach ($sites as $domain => $site) {
        if (preg_match('#^\*\.#', $domain)) {
          /**
           * match subdomains excluding @ or domains starting with . or /
           */
          $pattern = '#^(?:[^./@]+\.)*' . str_replace(array('*.','.'), array('','\.'), $domain) . '$#';
          if (preg_match($pattern, $_SERVER['HTTP_HOST'])) {
            return $site;
            break;
          }
        }
      }
			return $sites['*'];
		};

    $sqSite = $sqSites[$_SERVER['HTTP_HOST']] ?: $findSite($sqSites->toArray());

    $sqSiteConfig = SQ_ROOT."slashquery/sites/$sqSite/config.php";
    if (file_exists($sqSiteConfig)) {
			self::$sqSite = $sqSite;
			define('SITE_ROOT', SQ_ROOT . "slashquery/sites/$sqSite/");
      require_once($sqSiteConfig);
    } else {
      die("no configuration found for: $sqSite");
    }

    /**
		 * set time zone
		 */
		ini_set('date.timezone', (defined('SQ_TIME_ZONE') ? SQ_TIME_ZONE : 'UTC'));

    return self::$sqSite;
  }

}
