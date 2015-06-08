<?php

// define constants
define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', PROJECT_DIR .'/locale');
define('DEFAULT_LOCALE', 'en_US');

require_once('lib/gettext.inc');

$supported_locales = array('en_US', 'sr_CS', 'de_CH');
$encoding = 'UTF-8';

$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;

// gettext setup
T_setlocale(LC_MESSAGES, $locale);
// Set the text domain as 'messages'
$domain = 'messages';
bindtextdomain($domain, LOCALE_DIR);
// bind_textdomain_codeset is supported only in PHP 4.2.0+
if (function_exists('bind_textdomain_codeset'))
    bind_textdomain_codeset($domain, $encoding);
textdomain($domain);

header("Content-type: text/html; charset=$encoding");
