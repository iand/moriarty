<?php
/*
 * Set up required environment for the Phing build file.
 */
define('MORIARTY_DIR',dirname(dirname(__FILE__)). DIRECTORY_SEPARATOR);
define('MORIARTY_ARC_DIR',"/Users/andrew/Development/arc/");

require_once MORIARTY_DIR . '/tests/fakehttprequest.class.php';
require_once MORIARTY_DIR . '/tests/fakerequestfactory.class.php';

ini_set('include_path',
  ini_get('include_path')
  .PATH_SEPARATOR.MORIARTY_DIR
  .PATH_SEPARATOR.MORIARTY_ARC_DIR
);
?>