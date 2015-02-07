<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");
include_once PHPLIB_ROOTPATH."phplib.bootstrap.php";

class Config {
    /* Debug Settings */
    var $debug = true;
    var $dev = true;
    /* Database stuff */
    var $dbTimeoutHandlerEnabled = true;
    var $dbTimeoutSecs = 2;
    var $dbHost = 'localhost';
    var $dbUser = 'root'; 
    var $dbPass = '';
}

class LibConfig extends Config {
    /* Debug Settings */
    var $timer = true;
}

class WebConfig extends Config {
    /* Site Settings */
    var $offline = false;
    var $offline_message = 'This site is down for maintenance.<br /> Please check back again soon.';
    var $sitename = 'Web site name goes here';
    var $editor = 'tinymce';
    var $list_limit = 20;
    var $legacy = false;
    /* Debug Settings */
    var $debug_lang = false;
    /* Database Settings */
    var $dbtype = 'mysql';
    var $host = 'localhost';
    var $user = 'dbuser';
    var $password = 'crazypassword';
    var $db = 'dbname';
    var $dbprefix = 'pd_';
    /* Server Settings */
    var $live_site = '';
    var $secret = '61Of4ndTYReLxUnY';
    var $gzip = false;
    var $error_reporting = -1;
    //var $helpurl = 'http://help.joomla.org';
    var $xmlrpc_server = false;
    var $ftp_host = '127.0.0.1';
    var $ftp_port = 21;
    var $ftp_user = '';
    var $ftp_pass = '';
    var $ftp_root = '';
    var $ftp_enable = false;
    var $force_ssl = false;
    /* Locale Settings */
    var $offset = 0;
    var $offset_user = 0;
    /* Mail Settings */
    var $mailer = 'mail';
    var $mailfrom = 'cmaples77@gmail.com';
    var $fromname = 'Night Concierge';
    var $sendmail = '/usr/sbin/sendmail';
    var $smtpauth = false;
    var $smtpsecure = 'none';
    var $smtpport = 25;
    var $smtpuser = '';
    var $smtppass = '';
    var $smtphost = 'localhost';
    /* Cache Settings */
    var $caching = false;
    var $cachetime = 15;
    var $cache_handler = 'file';
    /* Meta Settings */
    var $MetaDesc = 'PHPLIB Web';
    var $MetaKeys = 'phplib, web';
    var $MetaTitle = true;
    var $MetaAuthor = true;
    /* SEO Settings */
    var $sef           = false;
    var $sef_rewrite   = false;
    var $sef_suffix    = false;
    /* Feed Settings */
    var $feed_limit   = 10;
    var $feed_email   = 'author';
    var $log_path = 'C:\\path\\to\\logs';
    var $tmp_path = 'C:\\path\\to\\tmp';
    /* Session Setting */
    var $lifetime = 15;
    var $session_handler = 'database';
}
?>
