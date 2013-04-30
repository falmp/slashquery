<?php
/**
 * sqi18n - /slashquery/core/classes/class.sqi18n.php
 * Multi-Language Support
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqi18n {
  private static $i18n = null;

  /**
   * init - load the translations
   *
   * @access protected
   * @return object i18n sqArray
   */
  protected static function init() {
    /**
     * Create a new translate sqArray object, defaults to locale/en/en.php
     */
    $t = new sqArray();

    /**
     * load defaults
     */
    include SQ_ROOT.'slashquery/core/locale/en/en.php';

    /**
     * load tranlations per site overloading defaults
     */
    $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

    if (isset($_COOKIE['sq_lang'])) {
      $lang = $_COOKIE['sq_lang'];
    }

    $lang_path = SITE_ROOT."locale/$lang/$lang.php";
    if (file_exists($lang_path)) {
      include sqTools::cleanPath($lang_path);
    } else {
      $lang = substr($lang, 0,2);
      $lang_path = SITE_ROOT."locale/$lang/$lang.php";
      if (file_exists($lang_path)) {
        include sqTools::cleanPath($lang_path);
      }
    }

    self::$i18n = $t;
  }

  /**
   * translate
   *
   * @param string $text
   * @return string translation if found
   */
  public static function translate($text) {
    if (is_null(self::$i18n)) {
      self::init();
    }
    switch (func_num_args()) {
      case 2:
        return ($t = self::$i18n[$text][func_get_arg(1)]) && is_object($t) ? null : $t;
        break;

      default :
        return ($t = self::$i18n[$text]) && is_object($t) ? null : $t;
    }
  }

}
