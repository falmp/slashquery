<?php
/**
 * JS & CSS compressor - /sqCompressor.php
 *
 * This file is called from nginx when the css|js are not found in memcache
 * here the scripts are compresed and stored on memcache so they can be serverd
 * very fast.
 *
 * to avoid caching or compressing set the env var SQ_ENV, nginx example:
 * fastcgi_param  SQ_ENV lab;
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

 /**
  * enable or disable the compressor
  * true/false
  */
define('SQ_COMPRESSOR', true);

/**
 *  false = no cache
 *  0 = never expire
 *  86400 = expire in 1 day
 */
define('SQ_COMPRESSOR_CACHE', 0);
define('MEMCACHE_HOST', '127.0.0.1');
define('MEMCACHE_PORT', 11211);

/**
 * remove the query in case exist.
 * this helps injections to work (codekit)
 */
$file = strtok($_SERVER['REQUEST_URI'], '?');

$file = __DIR__. str_replace(array('..','//'),'', urldecode($file));

if (!file_exists($file)) {
  exit("$file do not exists");
}

$file_info = pathinfo($file);
$type = $file_info['extension'];

/**
 * for nginx cache
 */
$key  = 'sq:'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

switch ($type) {
  case 'js':
    header('Content-Type: application/javascript; charset=UTF-8');
    $js = file_get_contents($file);
    /**
     * do not cache if SQ_ENV (set on nginx) is set
     */
    if (isset($_SERVER['SQ_ENV'])) {
      exit($js);
    }
    if (SQ_COMPRESSOR) {
      require_once 'slashquery/core/vendor/jsmin/jsmin.php';
      $js = trim( JSMin::minify($js) );
      #require_once 'slashquery/core/3rdParty/packer/class.JavaScriptPacker.php';
      #$js = new JavaScriptPacker($js, 'Normal', true, false);
      #$js = $js->pack();
      if (SQ_COMPRESSOR_CACHE !== false) {
        $memcache_obj = memcache_connect(MEMCACHE_HOST, MEMCACHE_PORT);
        memcache_set_compress_threshold($memcache_obj, 0);
        memcache_set($memcache_obj, $key, $js, 0, SQ_COMPRESSOR_CACHE);
      }
    }
    exit($js);
    break;

  case 'css':
    header('Content-type: text/css; charset=UTF-8');
    $css = file_get_contents($file);
    /**
     * do not cache if SQ_ENV (set on nginx) is set
     */
    if (isset($_SERVER['SQ_ENV'])) {
      exit($css);
    }
    if (SQ_COMPRESSOR) {
      $css = preg_replace('<
				\s*([@{}:;,]|\)\s|\s\()\s* | # Remove whitespace around separators, but keep space around parentheses.
				/\*([^*\\\\]|\*(?!/))+\*/ | # Remove comments that are not CSS hacks.
				[\n\r] # Remove line breaks.
				>x', '\1', $css);
      if (SQ_COMPRESSOR_CACHE !== false) {
        $memcache_obj = memcache_connect(MEMCACHE_HOST, MEMCACHE_PORT);
        memcache_set_compress_threshold($memcache_obj, 0);
        memcache_set($memcache_obj, $key, $css, 0, SQ_COMPRESSOR_CACHE);
      }
    }
    exit($css);
    break;
}
