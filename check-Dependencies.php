<?php
/**
 * Check for extensions needed to run slashquery properly
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

$required = array('curl','redis','memcache','sqlite3','session','posix','sockets','pcntl','openssl','mysqli','mysqlnd','mbstring','json','hash','gmp','intl','pcre','ctype','gd', 'SimpleXML','filter');

/**
 * we need php >= 5.4
 */
$phpv = phpversion();
if (strnatcmp($phpv,'5.4.0') >= 0) {
  echo str_pad("PHP version: $phpv", 30, '.', STR_PAD_RIGHT),"\033[1;37m\033[42m " . 'OK' . " \033[0m",PHP_EOL;
} else {
  echo str_pad("PHP >= 5.4: $phpv", 30, '.', STR_PAD_RIGHT),"\033[1;37m\033[41m " . 'NO' . " \033[0m",PHP_EOL;
}

function check($extension, $loaded) {
  if (in_array($extension, $loaded)) {
    echo str_pad($extension, 30, '.', STR_PAD_RIGHT),"\033[1;37m\033[42m " . 'OK' . " \033[0m",PHP_EOL;
  } else {
    echo str_pad($extension, 30, '.', STR_PAD_RIGHT),"\033[1;37m\033[41m " . 'NO' . " \033[0m",PHP_EOL;
  }
}

asort($required);
$loaded = get_loaded_extensions();
foreach ($required as $req) {
  check($req, $loaded);
}
