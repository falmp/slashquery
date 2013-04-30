<?php
/**
 * sqTPL - /slashquery/core/classes/class.sqTPL.php
 * template engine
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqTPL extends sqBase implements ArrayAccess {
  private $container = array();
  #private $tregex = '#\{(t)\s?([\w]*)}([^\{]*)\{/\1}#';
  /**
   * the idea is to return 3 elements
   * group 1 = t
   * group 2 = module
   * group 3 = text to translate
   */
  private $tregex = '#\{(t)\s?([\w]*)}((?:(?!\{\/t\}).)*)\{/\1}#';

  public function offsetExists($offset) {
    return isset($this->container[$offset]);
  }

  public function offsetGet($offset) {
    return isset($this->container[$offset]) ? $this->container[$offset] : false;
  }

  public function offsetSet($offset, $value) {
    if (!is_string($offset)) {
      throw new Exception('sqTPL offset missing', 0);
    }
    $this->container[$offset] = $value;
  }

  public function offsetUnset($offset) {
    unset($this->container[$offset]);
  }

  public function __construct(sqRouter $router, Closure $ACL) {
    /**
     * to monitor when the TPL initialize
     */
    sqLogger::log(__CLASS__, $router->module);

    $this->router = $router;
    $this->ACL = $ACL;

    /**
     * relative path so it can be used on the html templates
     */
    switch (true) {
      case $router->isCpanel:
        $this->template = 'slashquery/core/templates/cpanel/';
        break;

      case $router->xx1032:
        $this->template = 'slashquery/xx-10-32/templates/';
        break;

      default :
        $this->template = 'slashquery/sites/' . $router->site . '/templates/' . SQ_TEMPLATE_NAME . '/';
    }

    /**
     * in case user extends cpanel can add custom CSS / JS
     */
    if ($router->isCpanelExt) {
      $this->ext_template = 'slashquery/sites/' . $router->site . '/cpanel/' . $router->module . '/templates/';
    }
  }

  /**
   * getPath - path of template where normally you store the static content like
   * img/css/js
   *
   * @return string site + template
   */
  public function getPath($cPanel_extension=false) {
    if ($cPanel_extension) {
      return (defined('CDN_ENABLED') && CDN_ENABLED) ? sprintf('//%s/%s', CDN_VENDOR, $this->ext_template) : sprintf('//%s/%s', $_SERVER['HTTP_HOST'], $this->ext_template);
    } else {
      return (defined('CDN_ENABLED') && CDN_ENABLED) ? sprintf('//%s/%s', CDN_VENDOR, $this->template) : sprintf('//%s/%s', $_SERVER['HTTP_HOST'], $this->template);
    }
  }

  /**
   * Cache for the template using an engine (memcache/redis) defined on the
   * config.php
   *
   * @access public
   * @return DALMP_Cache object
   */
  public function _cache() {
    list($type, $host, $port, $compress) = @explode(':', SQ_TEMPLATE_CACHE_TYPE) + array(null, null, null, null);
    $cache = new DALMP_Cache($type);
    return $cache->host($host)->port($port)->compress($compress);
  }

  /**
   * translate
   *
   * @param string $text
   * @return string
   */
  public function translate($text){
    return preg_replace_callback($this->tregex, array($this, 'parser') , $text);
  }

  /**
   * parser - replace {} with translations
   *
   * @access protected
   * @param array $matches
   */
  protected function parser($matches) {
    switch (true) {
      case $matches[2]:
        return sqi18n::translate($matches[2], $matches[3]) ?: $matches[3];
        break;

      /**
       * search by on the current module fallback to globals
       */
      default :
        return sqi18n::translate($this->router->module, $matches[3]) ?: (sqi18n::translate($matches[3]) ?: $matches[3]);
    }
  }

  /**
   * display - renders the page
   *
   * trigered by sqView when $this->notify(); is called
   *
   * @params string $tpl
   */
  public function display($tpl) {
    ob_start();
    /**
     * process all the templates and store them on var $page
     */
    include sqTools::cleanPath(SQ_ROOT . $tpl);
    $page = ($this->filter ? $this->trimwhitespace(ob_get_clean()) : ob_get_clean());

    /**
     * if translate, parse the page
     */
    $page = preg_replace_callback($this->tregex, array($this, 'parser') , $page);

    /**
     * enable nginx cache
     *
     * cache will be updated with the new processed template in case a
     * modification.
     * (check cases where user sign in/out there should be no problem)
     */
    if ($this->cache) {
      $this->cacheURI($page, $this->cache);
    }
    echo $page;
  }

  /**
   * CacheURI - stores the page on cache so that ngix can serv it directly
   *
   * @param string $page
   * @param int $timeout
   */
  public function cacheURI($page, $timeout) {
    $timeout = ($timeout > 1) ? $timeout : 3600;
    $key = 'sq:'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    /**
     * check in a near feature for memcached_gzip_flag
     */
    return $this->_cache()->compress(0)->set($key, $page, $timeout);
  }

  /**
   * block - renders blocks and catch them if specified
   * $cache param used as the timeout
   *
   * @param string $block
   * @param int $cache
   */
  public function block($block, $cache = false) {
    /**
     * clean the input of the block
     */
    $cblock = sqTools::cleanPath(SQ_ROOT . $this->template . $block);

    /**
     * check if block to include exists, and if not, try to search for a
     * cPanel extension template
     */
    if (!file_exists($cblock) && $this->router->isCpanelExt) {
      $cblock = sqTools::cleanPath(SQ_ROOT . $this->ext_template . $block);
    }

    if ($cache) {
      /**
       * create a hash per block, if file changes a new has is created
       */
      $key = sha1_file(SQ_ROOT . $this->template . $block);
      $buffer = $this->_cache()->get($key);
      if (!$buffer) {
        ob_start();
        include $cblock;
        $buffer = ob_get_clean();

        /**
         * translate buffer
         */
        $buffer = preg_replace_callback($this->tregex, array($this, 'parser') , $buffer);

        $timeout = ($cache > 1) ? $cache : 3600;
        $this->_cache()->set($key, $buffer, $timeout);
      }
      echo $buffer;
    } else {
      include $cblock;
    }
  }

  /**
   * trimwhitespace - cleans & compress html
   *
   * @param html $buffer
   */
  public function trimwhitespace($buffer) {
    // Unify Line-Breaks to \n
    $buffer = preg_replace("/\015\012|\015|\012/", "\n", $buffer);

    // capture Internet Explorer Conditional Comments
    preg_match_all('#<!--\[[^\]]+\]>.*?<!\[[^\]]+\]-->#is', $buffer, $match);
    $script_blocks_IE = $match[0];
    $buffer = preg_replace('#<!--\[[^\]]+\]>.*?<!\[[^\]]+\]-->#is', '@@:SCRIPT_IE:@@', $buffer);

    // script
    preg_match_all('#<script[^>]+>.*?</script>#is', $buffer, $match);
    $script_blocks = $match[0];
    $buffer = preg_replace('#<script[^>]+>.*?</script>#is', '@@:SCRIPT:@@', $buffer);

    // style
    preg_match_all('#<style[^>]+>.*?</style>#is', $buffer, $match);
    $style_blocks = $match[0];
    $buffer = preg_replace('#<style[^>]+>.*?</style>#is', '@@:STYLE:@@', $buffer);

    // pre
    preg_match_all('#<pre[^>]*?>.*?</pre>#is', $buffer, $match);
    $pre_blocks = $match[0];
    $buffer = preg_replace('#<pre[^>]*?>.*?</pre>#is', '@@:PRE:@@', $buffer);

    // textarea
    preg_match_all('#<textarea[^>]*?>.*?</textarea>#is', $buffer, $match);
    $textarea_blocks = $match[0];
    $buffer = preg_replace('#<textarea[^>]*?>.*?</textarea>#is', '@@:TEXTAREA:@@', $buffer);

    // Strip all HTML-Comments
    $buffer = preg_replace('#<!--.*?-->#ms', '', $buffer);

    // remove spaces
    $buffer = preg_replace('#\s+#u', ' ', $buffer);
    $buffer = str_replace(array('> <', '> @@:', ':@@ <', ':@@ @@:'), array('><', '>@@:', ':@@<', ':@@@@:'), $buffer);

    // replace script blocks
    $this->replace('@@:SCRIPT_IE:@@', $script_blocks_IE, $buffer, 1);
    $this->replace('@@:STYLE:@@', $style_blocks, $buffer);
    $this->replace('@@:SCRIPT:@@', $script_blocks, $buffer);
    $this->replace('@@:PRE:@@', $pre_blocks, $buffer);
    $this->replace('@@:TEXTAREA:@@', $textarea_blocks, $buffer);

    return $buffer;
  }

  /**
   * replace matches created by trimwhitespace  method
   *
   * @param string $search
   * @param string $replace
   * @param html $buffer
   * @param int $rspaces
   */
  protected function replace($search, $replace, &$buffer, $rspaces = 0) {
    $len = strlen($search);
    $pos = 0;
    $count = count($replace);

    for ($i = 0; $i < $count; $i++) {
      // does the search-string exist in the buffer?
      $pos = strpos($buffer, $search, $pos);
      if ($pos !== false) {
        // replace the search-string
        $buffer = substr_replace($buffer, $replace[$i], $pos, $len);
        if ($rspaces) {
          $buffer = preg_replace('#\s+#u', ' ', $buffer);
          $buffer = str_replace('> <', '><', $buffer);
        }
      } else {
        break;
      }
    }
  }

  /**
   * includeJS
   *
   * @param js $script
   * @param int $minify
   */
  public function includeJS($script, $minify = false) {
    $js_file = sqTools::cleanPath(SQ_ROOT . $this->template . $script);
    if ($minify) {
      require_once SQ_ROOT . 'slashquery/core/3rdParty/jsmin/jsmin.php';
      $js = trim( JSMin::minify( file_get_contents($js_file) ) );
    } else {
      $js = file_get_contents($js_file);
    }
    echo '<script type="text/javascript">', $js, ' </script>';
  }

  /**
   * includeCSS
   *
   * @param css $script
   * @param int $compress
   */
  public function includeCSS($script, $compress = false) {
    $css_file = sqTools::cleanPath(SQ_ROOT . $this->template . $script);
    $css = file_get_contents($css_file);
    if ($compress) {
      $css = preg_replace('<
				\s*([@{}:;,]|\)\s|\s\()\s* | # Remove whitespace around separators, but keep space around parentheses.
				/\*([^*\\\\]|\*(?!/))+\*/ | # Remove comments that are not CSS hacks.
				[\n\r] # Remove line breaks.
				>x', '\1', $css);
    }
    echo '<style type="text/css">', $css, '</style>';
  }

  /**
   * htmlOptions - creates <select> options from array
   *
   * @param array $options
   * @param string $selected
   * @param bool $clean
   */
  public function htmlOptions($options, $selected = null, $clean = true) {
    static $opt;
    if ($clean) $opt = '';
    $g = 0;
    foreach ($options as $key => $value) {
      if (is_array($value)) {
        if ($g) {
          $opt .= '</optgroup>';
        }
        $opt .= '<optgroup label="' . $key . '">';
        $g = 1;
        $this->htmlOptions($value, $selected, false);
      } else {
        if ($selected == $key) {
          $opt .= '<option value="' . $key . '" selected="selected">' . $value . '</option>' . PHP_EOL;
        } else {
          $opt .= '<option value="' . $key . '">' . $value . '</option>' . PHP_EOL;
        }
      }
    }
    if ($g) {
      $opt .= '</optgroup>';
    }
    return $opt;
  }

}
