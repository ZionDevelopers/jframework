<?php
/**
 * jFramework
 *
 * @version 1.3.0
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

$SessionTimeout = (60 * 60 * 24 * 7); // 60 Seconds * 60 Minutes * 24 Hours * 7 Days
ini_set ( "session.cookie_domain", preg_replace ( "/www/i", "", $_SERVER ['SERVER_NAME'] ) );
ini_set ( 'session.cookie_lifetime', $SessionTimeout );
ini_set ( 'session.gc_maxlifetime', $SessionTimeout );
ini_set ( 'url_rewriter.tags', '' );
ini_set ( 'session.use_trans_sid', 0 );
ini_set ( 'session.save_path', SESSION_DIR );
ini_set ( 'session.name', 'sID' );
ini_set ( 'session.use_only_cookies', 1 );
ini_set ( 'session.cookie_httponly', 1 );
