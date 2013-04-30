<?php
/**
 * sqLocale - /slashquery/core/classes/class.sqLocale.php
 *
 * class that parses the templates and creates the locale files
 * example: sites/locale/en/en.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqLocale extends sqBase {

  const TPL_REGEX = '#\{(t)\s?([\w]*)}((?:(?!\{\/t\}).)*)\{/\1}#';

  /**
   * create
   *
   * @param string $iso iso-639-1
   * @param string $language
   * @param DALMP $db
   * @return boolean;
   */
  public static function create($iso, $language, $db) {

    $template_root = SITE_ROOT . 'templates/' . SQ_TEMPLATE_NAME . '/modules';

    /**
     * found all template modules for the site
     */
    $modules = array();
    if ($handle = opendir($template_root)) {
      /* This is the correct way to loop over the directory. */
      while (false !== ($dir = readdir($handle))) {
        if ($dir != '.' && $dir != '..') {
          $modules[$dir] = scandir("$template_root/$dir");
        }
      }
      closedir($handle);
    }

    /**
     * parse all the template files
     */
    $lang = array();
    foreach ($modules as $dir => $module) {
      foreach ($module as $template) {
        if ($template != '.' && $template != '..') {
          $buffer = file_get_contents("$template_root/$dir/$template");
          if (preg_match_all(self::TPL_REGEX, $buffer, $match)) {
            $lang[$dir] = array_map(function($item) use ($template) {return $template; }, array_flip($match[3]));
            ksort($lang[$dir]);
          }
        }
      }
    }

    /**
     * check if lang exists and merge recursive with the new one
     */
    $locale_dir = SITE_ROOT."/locale/$iso";
    if (!file_exists($locale_dir)) {
      mkdir($locale_dir,0755,true);
    }

    $locale_file = "$locale_dir/{$iso}.php";

    if (file_exists($locale_file)) {
      $t = array();
      include $locale_file;
      $lang = array_merge_recursive($lang, $t);
    }

    /**
		 * Save the locale / translations on DB helping to recreate the translation
		 * for a specific language.
		 * The 'locale/iso/iso.php' file, is stored on file system, therefore you have
		 * to replicate it in all your instances, this is only for cases where your
		 * site is load balanced or distributed on the cloud for example.
     */
    $data = json_encode(array($language => $lang));

		try {
      $db->PExecute('INSERT INTO sq_locale SET iso639=?, data=? ON DUPLICATE KEY UPDATE iso639=?', $iso, $data, $iso);
		} catch (Exception $e) {
			return false;
		}

    /**
     * prepare the locale xx/xx.php file and write to disk
     */
    $site = explode('/',SITE_ROOT);
    $locale = '<?php'.PHP_EOL."/* $iso - $language translation for ". end($site) .' */'.PHP_EOL;
    foreach ($lang as $module => $lang) {
      $locale .= PHP_EOL."/* module - $module */".PHP_EOL;
      $locale .= "\$t['".$module."'] = array();".PHP_EOL;
      foreach ($lang  as $t => $file) {
        if (is_array($file)) {
          $locale .= "\$t['".$module."']['".$t."'] = '$file[1]'; /* $file[0] */".PHP_EOL;
        } else {
          $locale .= "\$t['".$module."']['".$t."'] = '$t'; /* $file */".PHP_EOL;
        }
      }
    }
    return file_put_contents("$locale_dir/{$iso}.php", $locale);
  }

  /**
   * create
   *
   * @param string $iso iso-639-1
   * @return boolean;
   */
  public static function delete($iso, $db) {
    /**
     * Delete from DB later from disk
     */
		try {
      $db->PExecute('DELETE FROM sq_locale WHERE iso639=?', $iso);
		} catch (Exception $e) {
			return false;
		}
    $locale_dir = realpath(SITE_ROOT."/locale/$iso");
    if (file_exists($locale_dir)) {
      return sqTools::delTree($locale_dir);
    } else {
      return true;
    }
  }

}
