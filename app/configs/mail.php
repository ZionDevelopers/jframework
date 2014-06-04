<?php
/**
 * jFramework
 *
 * @version 1.3.0
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

/** PHPMAILER configuration example **/
$CONFIGS ['mail'] = array ();
$CONFIGS ['mail'] ['host'] = 'localhost';
$CONFIGS ['mail'] ['user'] = 'no-reply@localhost';
$CONFIGS ['mail'] ['pass'] = 'test';
$CONFIGS ['mail'] ['from_email'] = 'no-reply@localhost';
$CONFIGS ['mail'] ['from_name'] = SITE_NAME;
$CONFIGS ['mail'] ['copy'] = explode ( '|', SITE_EMAIL_COPIES );
