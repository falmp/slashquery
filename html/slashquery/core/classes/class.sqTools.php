<?php
/**
 * sqTools - /slashquery/core/classes/class.sqTools.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqTools {

  /**
   * signOut
   * close the session and delete cookies
   */
  public static function signOut() {
    sqSession::Start();
    sqSession::destroy();
		foreach ($_COOKIE as $key => $val) {
			setcookie($key, '', 1);
			setcookie($key, '', 1, '/');
      /**
       * to delete wildcard (leading dot).cookies
       */
      setcookie($key, '', 1, '/', $_SERVER['HTTP_HOST']);
		}
    header('HTTP/1.1 303 See Other');
    header('Cache-control: private');
    header('Location: /', true, 302);
    exit;
  }

  /**
   * postVars - check for posted vars and that are not empty
   *
   * usage: if(postVars('form_name','form_message')) [...]
   *
   * @return boolean
   */
  public static function postVars() {
    foreach (func_get_args() as $var) {
      if (!isset($_POST[$var]) || $_POST[$var] === '') return false;
    }
    return true;
  }

  /**
   * sanitize
   *
   * @param string $var The variable name you would like to check
   * @param string $type  alnum, alpha, digit, string
   * @param int $length The maximum length of the variable
   *
   * @return boolean
   */
  public static function sanitize(&$var, $type=null, $length=0) {
    $var = trim($var);
    if (is_numeric($var)) {
      $var = !strcmp(intval($var), $var) ? (int) $var : (!strcmp(floatval($var), $var) ? (float) $var : $var);
    }

    $length = (int) $length;
    if ($length && strlen($var) != $length) {
      return false;
    }

    switch ($type) {
      /**
       * Check for alphanumeric character(s)
       */
      case 'alnum':
        return ctype_alnum("$var");
        break;

      /**
       * Check for alphabetic character(s)
       */
      case 'alpha':
        return ctype_alpha("$var");
        break;

      /**
       * Check for numeric character(s)
       */
      case 'digit':
        return ctype_digit("$var");
        break;

      /**
       * Check for ASCII printable character(s)
       */
      case 'print':
        return preg_match('#^[\x20-\x7E]*$#', $var);
        break;

      case 'int':
        return is_int($var);
        break;

      case 'float':
        return is_float($var);
        break;

      case 'ip':
        return filter_var($var, FILTER_VALIDATE_IP);
        break;

      case 'ipv4':
        return filter_var($var, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        break;

      case 'ipv6':
        return filter_var($var, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        break;

      default:
        return is_string($var);
    }
  }

  /**
   * is number
   */
  public static function is_number($number) {
    $text = (string) $number;
    $textlen = strlen($text);
    if ($textlen == 0) return false;
      for ($i = 0; $i < $textlen; $i++) {
        $ch = ord($text{$i});
        if (($ch < 48) || ($ch > 57)) return false;
    }
    return true;
  }

  public static function format_filesize($bytes, $decimals = 1) {
    if ($bytes <= 1024) return $bytes . " Bytes";
    $unit = array('B','KB','MB','GB','TB','PB');
    return @round($bytes/pow(1024,($i=floor(log($bytes,1024)))),2).' '.$unit[$i];
  }

  /**
   * trimASCII - trim ALL the ASCII control characters (from 0 to 31 inclusive)
   *
   * @param string $haystack
   * @return string
   */
  public static function trimASCII($haystack) {
    $charlist = "\x00..\x1F";
    $out = '';
    $hlen = strlen($haystack);
    for ($i = 0; $i < $hlen; $i++) {
      if (strpos($charlist, $haystack[$i]) === false) $out .= $haystack[$i];
    }
    echo strlen($out);
    return $out;
  }

  /**
   * trimNS
   *
   * trim new line with the option to add spaces between lines
   *
   * @param string $string
   * @param int $addspace
   * @return string
   */
  public static function trimNS($string, $addSpace = 0) {
    return preg_replace('/\n\s+/', ($addSpace ? ' ' : ''), $string);
  }

  /**
   * validEmail
   *
   * verify email addresses
   *
   * @param email $email
   * @param boolean $checkDomain - uses checkdnsrr
   */
  public static function validEmail($email, $checkDomain = false) {
    $re = '/^[^<>\s\@]+(\@[^<>\s\@]+(\.[^<>\s\@]+)+)$/';
    if (preg_match($re, $email)) {
      if ($checkDomain && function_exists('checkdnsrr')) {
        $domain = explode('@', $email);
        $domain = function_exists('idn_to_ascii') ? idn_to_ascii($domain[1]) : $domain[1];
        return (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'SOA')) ? true : false;
      }
      return true;
    }
    return false;
  }

  /**
   * validDomain
   *
   * checks for a SOA record on the domain
   *
   * @param string $domain
   */
  public static function validDomain($domain) {
    return function_exists('idn_to_ascii') ? checkdnsrr(idn_to_ascii($domain), 'SOA') : checkdnsrr($domain, 'SOA');
  }

  /**
   * delTree
   *
   * Delete directory (recursive)
   *
   * @param string $dir
   */
  public static function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }

  /**
   * Tests if a string is standard 7-bit ASCII or not
   *
   * @param string $str
   * @return bool
   */
  public static function is_ascii($str) {
		return (preg_match('/[^\x00-\x7F]/S', $str) == 0);
	}

  /**
	 * Clean UTF-8 strings
	 *
	 * Ensures strings are UTF-8
	 *
	 * @access	public
	 * @param	string $str
	 * @return	string
	 */
	public static function cleanUTF8($str) {
		if (self::is_ascii($str) === FALSE) {
			$str = iconv('UTF-8', 'UTF-8//IGNORE', $str);
		}
		return $str;
	}

  /**
   * Clean Path removes ../, //

   * @param string $path
   * @return string
   */
  public static function cleanPath($path) {
    return str_replace(array('..','//'),'', urldecode($path));
  }

  /**
   * genpw - generate a password
   * min length 4
   *
   * @param int $length
   * @param bool $symbols
   *
   * @return string password
   */
  public static function genpw($length = 8, $use_symbols = 0) {
    $length = ($length < 4) ? 4 : $length;
    $alpha_lower = "abcdefghijklmnopqrstuvwxyz";
    $alpha_upper = strtoupper($alpha_lower);
    $numbers = '0123456789';
    $symbols = '~!@#$%^&*()_+-=[]{}:,.?<>\/';

    $L_max = round(70 * $length / 100); // 70%
    $U_max = ceil(15 * $length / 100);  // 15%
    $N_max = ceil(15 * $length / 100);  // 15%
    $S_max = floor($length / 8) ?: 1;

    $total = $L_max + $U_max + $N_max + $S_max;
    while ($length < $total) {
      $L_max = $L_max - 1;
      $total--;
    }

    $content = array('L' => array('characters' => $alpha_lower, 'min' => $L_max, 'max' => $L_max),
                     'U' => array('characters' => $alpha_upper, 'min' => 1, 'max' => $U_max),
                     'N' => array('characters' => $numbers, 'min' => 1, 'max' => $N_max),
                     'S' => array('characters' => $symbols, 'min' => 1, 'max' => $S_max));

    if (!$use_symbols) array_pop($content);

    $password = '';

    foreach ($content as $key => $value) {
      $password .= str_repeat($key, mt_rand($value['min'], $value['max']));
    }

    while (strlen($password) < $length) {
      $password .= 'L';
    }

    $password = str_shuffle($password);

    $out = '';
    for ($i = 0; $i < strlen($password); $i++) {
      $out .= $content[$password[$i]]['characters'][mt_rand(0, strlen($content[$password[$i]]['characters']) - 1)];
    }
    return $out;
  }

  /**
   * hasher - CRYPT_BLOWFISH
   * $cost range 4-31
   *
   * @param string $string
   * @param string $hash
   * @return boolean if $hash passed
   */
  public static function hasher($string, $hash=null) {
    if ($hash) {
      return crypt($string, $hash) ==  $hash;
    }
    $cost = defined('SQ_HASHER_COST') ?: 8;
    $salt = '$2a$' . str_pad($cost, 2, '0', STR_PAD_LEFT) . '$' . substr(strtr(base64_encode(openssl_random_pseudo_bytes(16)), '+', '.'), 0, 22);
    return crypt($string, $salt);
  }

  /**
   * Salt - variable length
   *
   * @param int $length
   * @return HEX random salt
   */
  public static function salt($length=8) {
    $l = ($length < 8) ? 4 : ceil($length/2);
    $rnd = bin2hex(openssl_random_pseudo_bytes($l));
    return substr($rnd, 0, $length);
  }

  /**
   * ASCII Salt
   *
   * @param int $length
   * @return ASCII 32-126 random salt
   */
  public static function saltASCII($length) {
    $random = '';
    for ($i = 0; $i < $length; $i++) {
      $random .= chr(mt_rand(32, 126));
    }
    return strtr($random, "'", '"');
  }

  /**
   * sha132
   *
   * @param string $s
   * @param int $f
   * @return sha1 encoded base 32 for string of file
   */
  public static function sha132($s, $f=null) {
    $BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $input = $f ? sha1_file($s, true) : sha1($s, true);
    $output = '';
    $position = 0;
    $storedData = 0;
    $storedBitCount = 0;
    $index = 0;

    while ($index < strlen($input)) {
      $storedData <<= 8;
      $storedData += ord($input[$index]);
      $storedBitCount += 8;
      $index += 1;

      //take as much data as possible out of storedData
      while ($storedBitCount >= 5) {
        $storedBitCount -= 5;
        $output .= $BASE32_ALPHABET[$storedData >> $storedBitCount];
        $storedData &= ((1 << $storedBitCount) - 1);
      }
    } //while

    //deal with leftover data
    if ($storedBitCount > 0) {
      $storedData <<= (5-$storedBitCount);
      $output .= $BASE32_ALPHABET[$storedData];
    }

    return $output;
  }

  /**
   * mergeArrays
   *
   * @param array $a1
   * @param array $a2
   *
   * @return array
   */
  public static function mergeArrays($a1, $a2) {
    foreach ($a2 as $key => $value) {
      if (array_key_exists($key, $a1) && is_array($value))
        $a1[$key] = self::MergeArrays($a1[$key], $a2[$key]);
      else
        $a1[$key] = $value;
    }
    return $a1;
  }

  /**
   * jStatus
   *
   * @param boolean/string $status
   * @param int $boolean
   *
   * @return json
   */
  public static function jStatus($status=false, $boolean=false) {
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Content-Type: application/json; charset=UTF-8');

    echo ($boolean) ? json_encode(array('status' => $status)) : json_encode($status);
    exit;
  }

  /**
   * CaptchaXX1032 - creates a captcha for the XX1032
   *
   * @param int $expiry - number of days the captcha is valid
   *
   * @return array time, captcha
   */
  public static function captchaXX1032($expiry = 3) {
    $time = time() + ($expiry * 24 * 60 * 60);
    $r32  = bin2hex(openssl_random_pseudo_bytes(16));
    /**
     *  store the time in hex (8 digits) intercalated on the random 32 string
     */
    $htime = dechex($time);
    $time_pos = array(17,19,21,23,25,27,29,31);
    for ($i=0; $i < 8; $i++) {
    	$r32[$time_pos[$i]] = $htime[$i];
    }
    return array($time, $r32);
  }

  /**
   *
   * validCaptchaXX1032
   *
   * @param int $expiry
   * @param string $captcha
   *
   * @return boolean
   */
  public static function validCaptchaXX1032($expiry, $captcha) {
    if (strlen($expiry) != 10)
      return false;

    if (strlen($captcha) != 32)
      return false;

    $time = '00000000';
    $time_pos = array(17,19,21,23,25,27,29,31);
    for ($i=0; $i < 8; $i++) {
      $time[$i] = $captcha[$time_pos[$i]];
    }
    $time = hexdec($time);
    return $time != $expiry ? false : ( time() > $time ? false : true);
  }

  /**
	 * sendXX1032
	 *
	 * Send confirmation codes email
   *
   * @param string $type - NU (New User), RP (Reset Password)
	 * @param array $captcha
	 * @pararm array $from
	 * @param array $to
	 * @param string $subject
	 * @param string $body

	 * @return true on success
	 */
  public static function sendXX1032($type, array $captcha, array $from, array $to, $subject, $body) {
    $idn2ascii = function($a) {
      foreach ($a as $key => $domain) {
        if (strpos($domain, '@') !== false) {
          $d = explode('@', $domain);
          $a[$key] = $d[0] . '@' . (function_exists('idn_to_ascii') ? idn_to_ascii($d[1]) : $d[1]);
        }
      }
      return $a;
    };

		$mail = new sqMail();
		$mail->From = $idn2ascii($from);
		$mail->To = $idn2ascii($to);
		$mail->Subject = $subject;

		$captcha = implode('-', $captcha);
		$url = 'http://'.$_SERVER['HTTP_HOST']."/$type-$captcha";

		$mail->Body = strpos($body, '__URL__') === false ? "$body\n\n$url\n\n" : str_replace('__URL__', "\n\n$url\n\n", $body);

		return $mail->Send();
	}

  /**
   * date in format ISO 8601 with Zulu
   *
   * @return ISO 8601 date example: 2012-02-10T12:07:24Z
   */
  public static function dateISO8601Z() {
    return gmDate('Y-m-d\TH:i:s\Z');
  }

  /**
   * curl POST
   *
   * @param string $url
   * @param array $vars
   * @param boolean $header
   * @return boolean or string
   */
  public static function curlPOST($url, array $vars, $header=false) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, '- slashquery -');

    $rs = curl_exec($ch);
    curl_close($ch);
    return ($rs === false) ? false : $rs;
  }

  /**
   * getIPv4- try to return the public IP
   *
   * @return IPv4
   */
  public static function getIPv4() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
      if (array_key_exists($key, $_SERVER) === true) {
        foreach (explode(',', $_SERVER[$key]) as $ip) {
          $ip = trim($ip);
          if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
            return $ip;
          }
        }
      }
    }
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
  }

  /**
   * parseURL - rfc3986
   *
   * @see rfc3986
   * @return array
   */
  public static function parseURL($uri) {
    $parts = array (
        'scheme' => '',
        'host' => '',
        'port' => '',
        'user' => '',
        'pass' => '',
        'path' => '',
        'query' => '',
        'fragment' => ''
    );

    preg_match( '@^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?@', $uri, $matches );

    if (array_key_exists( 2, $matches )) $parts['scheme'] = strtolower($matches[2]);
    if (array_key_exists( 4, $matches )) $authority = $matches[4];
    if (array_key_exists( 5, $matches )) $parts['path'] = $matches[5];
    if (array_key_exists( 7, $matches )) $parts['query'] = $matches[7];
    if (array_key_exists( 9, $matches )) $parts['fragment'] = $matches[9];

    /* Extract username, password, host and port from authority */
    preg_match('"(([^:@]*)(:([^:@]*))?@)?([^:]*)(:(.*))?"', $authority, $matches);

    if (array_key_exists( 2, $matches )) $parts['user'] = $matches[2];
    if (array_key_exists( 4, $matches )) $parts['pass'] = $matches[4];
    if (array_key_exists( 5, $matches )) $parts['host'] = strtolower($matches[5]);
    if (array_key_exists( 7, $matches )) $parts['port'] = $matches[7];

    return $parts;
  }

}
