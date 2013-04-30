<?php
/**
 * File that helps to setup (create/drop) tables required by SlashQuery it ask
 * for an email the one will be used for login to the /cpanel with the returned
 * password. (database needs to be created previously)
 *
 * The parameters for the database are taken from the  the config.pph file of
 * the site where it is run.
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

echo "Enter admin email:\n"; // Output - prompt user
$admin = trim(fgets(STDIN)); // Read the input
$re = '/^[^<>\s\@]+(\@[^<>\s\@]+(\.[^<>\s\@]+)+)$/';
if (!preg_match($re, $admin)) {
  exit('no valid email');
}

define('SQ_ROOT', __DIR__ .'/');
$sqSite = basename(SQ_ROOT);

require_once '../../config.php';
require_once 'config.php';
require_once '../../core/classes/class.sqTools.php';
require_once '../../core/vendor/dalmp/dalmp.php';

/**
 * @var DALMP
 */
$db = new DALMP(DSN);
$db->PExecute('SET time_zone="+00:00"');

$drop=1;
$tables = array('sq_users_openids','sq_users_logs','sq_users_roles','sq_ACLs','sq_config','sq_modules','sq_roles','sq_users','sq_users_openids','sq_locale','dalmp_sessions');
if ($drop) {
  foreach ($tables as $t) {
    echo "Droping  table $t".PHP_EOL;
    $db->Execute("DROP TABLE IF EXISTS $t");
  }
}

/*******************************************************************************
 *
 * sq_Config
 */
try {
  echo 'creating sq_Config ... ';
  $rs = $db->Execute("
  CREATE TABLE `sq_config` (
    `config_name` VARCHAR(255) NOT NULL DEFAULT '',
    `config_value` VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`config_name`),
    KEY `cname` (`config_name`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
} catch (Exception $e) {
  exit('Could not create table sq_Config');
}

try {
  $rs = $db->Execute("INSERT INTO sq_config VALUES
                    ('cpanel_email_resetPassword_from', 'no-reply@__HOST__'),
                    ('cpanel_email_resetPassword_from_name', 'Admin __SITE__'),
                    ('cpanel_email_resetPassword_subject', '__SITE__ - reset your password '),
                    ('cpanel_email_resetPassword_body', 'A request to reset your password was received.\n\nTo change your password, you must visit the following address: __URL__If you did not make this request, you can safely ignore this email.'),
                    ('avoid_logging_twice', 1),
                    ('allow_force_login', 1),
                    ('allow_signup', 1)");
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not load data on table sq_Config');
}

/*******************************************************************************
 *
 *sq_modules
 *
 */
try {
  echo 'creating sq_modules ... ';
  $rs = $db->Execute("
  CREATE TABLE `sq_modules` (
  `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` VARCHAR(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `cpanel` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `mdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`name`),
  UNIQUE KEY `unique` (`name`,`cpanel`),
  KEY `id` (`id`),
  KEY `status` (`status`),
  KEY `cpanel` (`cpanel`)
  ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
} catch (Exception $e) {
  exit('Could not load data on table sq_modules');
}

try {
  $rs = $db->Execute("INSERT INTO sq_modules VALUES
                     (1, 'cpanel', 'core', 1, NOW(),1),
                     (2, 'ACL', 'core', 1, NOW(), 1)");
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not load data on table sq_modules');
}

/*******************************************************************************
 *
 *sq_roles
 *
 */
try {
  echo 'creating sq_roles ... ';
  $rs = $db->Execute("
  CREATE TABLE `sq_roles` (
  `rid` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`rid`),
  UNIQUE KEY `name` (`name`),
  KEY `rid` (`rid`)
  ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
} catch (Exception $e) {
  exit('Could not create table sq_roles');
}

try {
  $rs = $db->Execute("INSERT INTO `sq_roles` VALUES ('3', 'administrator'), ('1', 'anonymous users'), ('2', 'authenticated users');");
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not load data on table sq_modules');
}

/*******************************************************************************
 *
 * sq_ACLs
 *
 */
try {
  echo 'creating sq_ACLs ... ';
  $rs = $db->Execute("
  CREATE TABLE `sq_ACLs` (
  `rid` SMALLINT(5) UNSIGNED NOT NULL,
  `ACL` TEXT CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `rid` (`rid`),
  CONSTRAINT `sq_ACLs_ibfk_1` FOREIGN KEY (`rid`) REFERENCES `sq_roles` (`rid`) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not create table sq_ACLs');
}

/*******************************************************************************
 *
 * sq_users
 * sex - http://en.wikipedia.org/wiki/ISO_5218
 * 0 = not known,
 * 1 = male,
 * 2 = female,
 * 9 = not applicable.
 *
 */
try {
  echo 'creating sq_users ... ';
  $rs = $db->Execute("
  CREATE TABLE `sq_users` (
  `uid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `sex` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `password` CHAR(60) NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `login_count` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  `cdate` DATETIME NOT NULL,
  `captcha` BINARY(16) NOT NULL DEFAULT '',
  `cookie` BINARY(20) DEFAULT '',
  `cookie_timeout` datetime NOT NULL,
  `mdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uuid` BINARY(16) NOT NULL,
  PRIMARY KEY (`uid`,`email`,`uuid`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `email` (`email`),
  KEY `captcha` (`captcha`),
  KEY `sex` (`sex`),
  KEY `status` (`status`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
} catch (Exception $e) {
  exit('Could not create table sq_users');
}

try {
  $password = sqTools::genpw(16,1);
  $hash = hash('sha256', $admin.sha1($password));
  $dbpass = sqTools::hasher($hash);
  $rs = $db->PExecute("INSERT INTO sq_users SET uid=1, email=?, password=?, status=1, cdate=NOW(), uuid=UNHEX(REPLACE(UUID(), '-', ''))", $admin, $dbpass);
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not load data on table sq_users');
}

/*******************************************************************************
 *
 * sq_users_logs
 *
 */
try {
  echo 'creating sq_users_logs ... ';
  $rs = $db->Execute("
  CREATE TABLE `sq_users_logs` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) UNSIGNED NOT NULL,
  `cdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` INT(11) UNSIGNED NOT NULL,
  `host` VARCHAR(255) NOT NULL,
  `ua` VARCHAR(255) NOT NULL,
  `referer` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `ip` (`ip`),
  KEY `host` (`host`),
  KEY `ua` (`ua`),
  CONSTRAINT `sq_users_logs_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `sq_users` (`uid`) ON DELETE CASCADE
  ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not create table sq_users_logs');
}

/*******************************************************************************
 *
 * sq_users_roles
 *
 */
try {
  echo 'creating sq_users_roles ... ';
  $rs = $db->Execute("
  CREATE TABLE `sq_users_roles` (
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `rid` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`,`rid`),
  KEY `rid` (`rid`),
  KEY `uid` (`uid`),
  CONSTRAINT `sq_users_roles_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `sq_users` (`uid`) ON DELETE CASCADE,
  CONSTRAINT `sq_users_roles_ibfk_2` FOREIGN KEY (`rid`) REFERENCES `sq_roles` (`rid`) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not create table sq_users_roles');
}

/*******************************************************************************
 *
 * sq_users_openids
 *
 */
try {
  echo 'creating sq_users_openids ... ';
  $rs = $db->Execute("
  CREATE TABLE `sq_users_openids` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `openid` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`openid`),
  UNIQUE KEY `openid` (`openid`) USING BTREE,
  KEY `uid` (`uid`),
  CONSTRAINT `sq_users_openids_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `sq_users` (`uid`) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not create table sq_users_openids');
}

/*******************************************************************************
 *
 * sq_locale
 *
 */
try {
  echo 'creating sq_locale... ';
  $rs = $db->Execute("
  CREATE TABLE `sq_locale` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `iso639` char(2) COLLATE utf8_bin NOT NULL,
  `data` longtext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`,`iso639`),
  UNIQUE KEY `iso639` (`iso639`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not create table sq_locale');
}

/*******************************************************************************
 *
 * dalmp_sessions
 *
 */
try {
  echo 'creating dalmp_sessions ... ';
  $rs = $db->Execute("
  CREATE TABLE `dalmp_sessions` (
  `sid` VARCHAR(40) NOT NULL DEFAULT '',
  `expiry` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `data` LONGTEXT,
  `ref` VARCHAR(255) DEFAULT NULL,
  `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`),
  KEY `ref` (`ref`),
  KEY `sid` (`sid`),
  KEY `expiry` (`expiry`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
  if ($rs) { echo 'Ok'.PHP_EOL; }
} catch (Exception $e) {
  exit('Could not create table dalmp_sessions');
}

/**
 * write site salt in the config.php file
 */
$siteSALT = sqTools::saltASCII(64);

file_put_contents('config.php', implode('',
  array_map(function($data) use ($siteSALT) {
    return stristr($data,'SITE_SALT') ? "define('SITE_SALT', '$siteSALT');".PHP_EOL : $data;
  }, file('config.php'))
));

echo "admin: $admin".PHP_EOL;
echo "password: $password".PHP_EOL;
