<?php
/**
 * Tools to do almost all things
 * @!created 2010-07-24 10:25 AM
 * @!updated 2011-02-03 10:42 AM
 * @version 1.1.1
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
class tools {
	public static $mail_configs = array (
			'host' => 'localhost',
			'user' => 'test',
			'pass' => 'test',
			'from_name' => 'Test',
			'from_email' => 'test@test.com',
			'copy' => array (
					'test@test.com' 
			) 
	);

	public static $db = null;
	public static $states = array (
			'AC' => 'Acre',
			'AL' => 'Alagoas',
			'AP' => 'Amapa',
			'AM' => 'Amazonas',
			'BA' => 'Bahia',
			'CE' => 'Ceara',
			'DF' => 'Distrito Federal',
			'ES' => 'Espirito Santo',
			'GO' => 'Goias',
			'MA' => 'Maranhao',
			'MT' => 'Mato Grosso',
			'MS' => 'Mato Grosso do Sul',
			'MG' => 'Minas Gerais',
			'PA' => 'Para',
			'PB' => 'Paraiba',
			'PR' => 'Parana',
			'PE' => 'Pernambuco',
			'PI' => 'Piaui',
			'RJ' => 'Rio de Janeiro',
			'RN' => 'Rio Grande do Norte',
			'RS' => 'Rio Grande do Sul',
			'RO' => 'Rondonia',
			'RR' => 'Roraima',
			'SC' => 'Santa Catarina',
			'SP' => 'Sao Paulo',
			'SE' => 'Sergipe',
			'TO' => 'Tocantins' 
	);
	
	/**
	 * To Send email
	 *
	 * @param $email string        	
	 * @param $subject string        	
	 * @param $body string        	
	 * @param $charset string        	
	 */
	public static function mail($email, $subject, $body) {
		require_once (LIBRARY_DIR . "/mailer/class.phpmailer.php");
		
		$Sent = false;
		
		$mail = new PHPMailer ( true );
		$mail->PluginDir = LIBRARY_DIR . '/mailer/';
		$mail->IsSMTP (); // telling the class to use SMTP
		
		try {
			$mail->CharSet = CHARSET;
			$mail->SMTPAuth = true; // enable SMTP authentication
			$mail->Host = self::$mail_configs ['host']; // sets the SMTP server
			$mail->Username = self::$mail_configs ['user']; // SMTP account username
			$mail->Password = self::$mail_configs ['pass']; // SMTP account password
			$mail->SetFrom ( self::$mail_configs ['from_email'], self::$mail_configs ['from_name'] );
			$mail->Subject = $subject;
			$mail->AltBody = "Para ver esta mensagem, favor usar um leitor de e-mail compativel com HTML!";
			
			$mail->MsgHTML ( $body );
			$mail->AddAddress ( $email );
			
			foreach ( self::$mail_configs ['copy'] as $copy ) {
				$mail->AddBCC ( $copy );
			}
			
			$Sent = $mail->Send ();
		} catch ( Exception $e ) {
			$Sent = false;
			trigger_error ( $e->getMessage () . ': ' . $e->getTraceAsString (), E_USER_WARNING );
		}
		
		return $Sent;
	}
	
	/**
	 * To Debug
	 *
	 * @param $any mixed        	
	 */
	public static function debug() {
		if (DEBUG) {
			foreach ( func_get_args () as $arg ) {
				echo '<pre>' . print_r ( $arg, true ) . '</pre>';
			}
		}
	}
	
	/**
	 * To report error
	 *
	 * @param $error integer        	
	 */
	public static function error($error = 404) {
		self::redir ( BASE_DIR . '/errors/' . $error . '?referer=' . @$_SERVER ['REQUEST_URI'] );
	}
	
	/**
	 *
	 * @param $file string        	
	 */
	public static function getContents($file) {
		$contents = 'nothing';
		if (file_exists ( $file )) {
			ob_start ();
			require_once ($file);
			$contents = ob_get_contents ();
			ob_end_clean ();
		}
		return $contents;
	}
	
	/**
	 * To format a value into Reais
	 *
	 * @param $price string        	
	 * @param $noCents boolean        	
	 * @return string
	 */
	public static function formatPrice($price, $noCents = false) {
		$newPrice = 'R$ ' . number_format ( self::price2Float ( $price ), 2, ",", "." );
		if ($noCents) {
			$newPrice = explode ( ',', $newPrice );
			$newPrice = $newPrice [0];
		}
		return $newPrice;
	}
	
	/**
	 * Format price to Plan Style
	 *
	 * @param $price string        	
	 * @return string
	 */
	public static function formatPlanPrice($price) {
		$old = self::formatPrice ( $price );
		$find = '/(R\$) ([0-9]{2,3})([,])([0-9]{2})/i';
		$replace = '<span class="plan_price_currency">R$</span><span class="plan_price_major">$2</span><span class="plan_price_comma">,</span><span class="plan_price_minor">$4</span>';
		return preg_replace ( $find, $replace, $old );
	}
	
	/**
	 * To clean a formated Price into Float
	 *
	 * @param
	 *        	$value
	 */
	public static function price2Float($value) {
		$string = ( string ) $value;
		
		if (strpos ( $string, "." ) !== false && strpos ( $string, "," ) !== false) {
			$string = str_replace ( ".", "", $string );
			$string = str_replace ( ",", ".", $string );
		} elseif (strpos ( $string, "." ) === false && strpos ( $string, "," ) !== false) {
			$string = str_replace ( ",", ".", $string );
		}
		return ( float ) $string;
	}
	
	/**
	 * Format area
	 *
	 * @param $value string        	
	 * @return string
	 */
	public static function formatArea($value) {
		$value = number_format ( $value, 2, ',', '.' );
		$value = explode ( ',', $value );
		$value = array_shift ( $value );
		return $value;
	}
	
	/**
	 * To get a file extension
	 *
	 * @param
	 *        	$file
	 * @return string
	 */
	public static function fileType($file) {
		$ext = explode ( ".", $file );
		$ext = $ext [count ( $ext ) - 1];
		return strtolower ( $ext );
	}
	
	/**
	 * To check if this file request has maded by AJAX
	 *
	 * @param $quit boolean        	
	 * @return boolean
	 */
	public static function checkAjax($quit = true) {
		$result = false;
		
		if (isset ( $_SERVER ["HTTP_X_REQUESTED_WITH"] )) {
			if ($_SERVER ["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
				if ($quit) {
					exit ( 'Access denied' );
				}
			} else {
				$result = true;
			}
		} else {
			if ($quit) {
				exit ( 'Access denied' );
			}
		}
		
		return $result;
	}
	
	/**
	 * Convert XML to ARRAY
	 *
	 * @param $xml string        	
	 * @param $recursive boolean        	
	 */
	public static function XML2Array($xml, $recursive = false) {
		if (! $recursive) {
			$array = simplexml_load_string ( $xml );
		} else {
			$array = $xml;
		}
		
		$newArray = array ();
		$array = ( array ) $array;
		foreach ( $array as $key => $value ) {
			$value = ( array ) $value;
			if (isset ( $value [0] )) {
				$newArray [$key] = trim ( $value [0] );
			} else {
				$newArray [$key] = self::XML2Array ( $value, true );
			}
		}
		return $newArray;
	}
	
	/**
	 * To convert Array TO XML
	 *
	 * @param $array array        	
	 * @param $version string        	
	 * @param $encoding string        	
	 * @param $rev boolean        	
	 * @return string
	 */
	public static function Array2XML(array $array, $version = "1.0", $encoding = "utf-8", $rev = false) {
		if (! $rev) {
			$xml = "<?xml version=\"" . $version . "\" encoding=\"" . $encoding . "\"?>\n\t<root>\n\t";
		} else {
			$xml = "";
		}
		
		foreach ( $array as $key => $value ) {
			if (is_numeric ( $key )) {
				$key = "_" . $key;
			}
			
			if (is_array ( $value )) {
				$xml .= "\t<" . str_replace ( '/', '-', $key ) . ">\n\t" . self::Array2XML ( $value, $version, $encoding, true ) . "\n\t</" . $key . ">\n";
			} else {
				$xml .= "\t<" . str_replace ( '/', '-', $key ) . ">" . $value . "</" . $key . ">\n";
			}
		}
		
		if (! $rev) {
			return $xml . "\n\t</root>";
		} else {
			return $xml;
		}
	}
	/**
	 * To validate an brazillian CPF (Document)
	 *
	 * @param $cpf string        	
	 * @return string
	 */
	public static function validateCPF($cpf) {
		// Retirar todos os caracteres que nao sejam 0-9
		$s = "";
		$s = preg_replace ( "/([^0-9])/", "", $cpf );
		
		$cpf = $s;
		if (strlen ( $cpf ) != 11) {
			return false;
		} elseif ($cpf == "00000000000") {
			return false;
		} elseif ($cpf == "11111111111") {
			return false;
		} elseif ($cpf == "22222222222") {
			return false;
		} elseif ($cpf == "33333333333") {
			return false;
		} elseif ($cpf == "44444444444") {
			return false;
		} elseif ($cpf == "55555555555") {
			return false;
		} elseif ($cpf == "66666666666") {
			return false;
		} elseif ($cpf == "77777777777") {
			return false;
		} elseif ($cpf == "88888888888") {
			return false;
		} elseif ($cpf == "99999999999") {
			return false;
		} else {
			$number [1] = intval ( substr ( $cpf, 1 - 1, 1 ) );
			$number [2] = intval ( substr ( $cpf, 2 - 1, 1 ) );
			$number [3] = intval ( substr ( $cpf, 3 - 1, 1 ) );
			$number [4] = intval ( substr ( $cpf, 4 - 1, 1 ) );
			$number [5] = intval ( substr ( $cpf, 5 - 1, 1 ) );
			$number [6] = intval ( substr ( $cpf, 6 - 1, 1 ) );
			$number [7] = intval ( substr ( $cpf, 7 - 1, 1 ) );
			$number [8] = intval ( substr ( $cpf, 8 - 1, 1 ) );
			$number [9] = intval ( substr ( $cpf, 9 - 1, 1 ) );
			$number [10] = intval ( substr ( $cpf, 10 - 1, 1 ) );
			$number [11] = intval ( substr ( $cpf, 11 - 1, 1 ) );
			
			$sum = 10 * $number [1] + 9 * $number [2] + 8 * $number [3] + 7 * $number [4] + 6 * $number [5] + 5 * $number [6] + 4 * $number [7] + 3 * $number [8] + 2 * $number [9];
			$sum = $sum - (11 * (intval ( $sum / 11 )));
			
			if ($sum == 0 || $sum == 1) {
				$result1 = 0;
			} else {
				$result1 = 11 - $sum;
			}
			
			if ($result1 == $number [10]) {
				$sum = $number [1] * 11 + $number [2] * 10 + $number [3] * 9 + $number [4] * 8 + $number [5] * 7 + $number [6] * 6 + $number [7] * 5 + $number [8] * 4 + $number [9] * 3 + $number [10] * 2;
				$sum = $sum - (11 * (intval ( $sum / 11 )));
				
				if ($sum == 0 || $sum == 1) {
					$result2 = 0;
				} else {
					$result2 = 11 - $sum;
				}
				if ($result2 == $number [11]) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
	
	/**
	 * To validate an document (CPF or CNPJ)
	 *
	 * @param $doc string        	
	 * @param boolean
	 */
	public static function validateDoc($doc) {
		$doc = preg_replace ( "/([^0-9])/", "", $doc );
		$result = false;
		
		if (strlen ( $doc ) == 11) {
			$result = self::validateCPF ( $doc );
		} elseif (strlen ( $doc ) == 14) {
			$result = self::validateCNPJ ( $doc );
		}
		
		return $result;
	}
	
	/**
	 * Validate CNPJ
	 *
	 * @param $cnpj string        	
	 * @return string
	 */
	public static function validateCNPJ($cnpj) {
		$s = "";
		$s = preg_replace ( "/([^0-9])/", "", $cnpj );
		
		$cnpj = $s;
		if (strlen ( $cnpj ) != 14) {
			return false;
		} elseif ($cnpj == "00000000000000") {
			return false;
		} elseif ($cnpj == "11111111111111") {
			return false;
		} elseif ($cnpj == "22222222222222") {
			return false;
		} elseif ($cnpj == "33333333333333") {
			return false;
		} elseif ($cnpj == "44444444444444") {
			return false;
		} elseif ($cnpj == "55555555555555") {
			return false;
		} elseif ($cnpj == "66666666666666") {
			return false;
		} elseif ($cnpj == "77777777777777") {
			return false;
		} elseif ($cnpj == "88888888888888") {
			return false;
		} elseif ($cnpj == "99999999999999") {
			return false;
		} else {
			$number [1] = intval ( substr ( $cnpj, 1 - 1, 1 ) );
			$number [2] = intval ( substr ( $cnpj, 2 - 1, 1 ) );
			$number [3] = intval ( substr ( $cnpj, 3 - 1, 1 ) );
			$number [4] = intval ( substr ( $cnpj, 4 - 1, 1 ) );
			$number [5] = intval ( substr ( $cnpj, 5 - 1, 1 ) );
			$number [6] = intval ( substr ( $cnpj, 6 - 1, 1 ) );
			$number [7] = intval ( substr ( $cnpj, 7 - 1, 1 ) );
			$number [8] = intval ( substr ( $cnpj, 8 - 1, 1 ) );
			$number [9] = intval ( substr ( $cnpj, 9 - 1, 1 ) );
			$number [10] = intval ( substr ( $cnpj, 10 - 1, 1 ) );
			$number [11] = intval ( substr ( $cnpj, 11 - 1, 1 ) );
			$number [12] = intval ( substr ( $cnpj, 12 - 1, 1 ) );
			$number [13] = intval ( substr ( $cnpj, 13 - 1, 1 ) );
			$number [14] = intval ( substr ( $cnpj, 14 - 1, 1 ) );
			
			$sum = $number [1] * 5 + $number [2] * 4 + $number [3] * 3 + $number [4] * 2 + $number [5] * 9 + $number [6] * 8 + $number [7] * 7 + $number [8] * 6 + $number [9] * 5 + $number [10] * 4 + $number [11] * 3 + $number [12] * 2;
			
			$sum = $sum - (11 * (intval ( $sum / 11 )));
			
			if ($sum == 0 || $sum == 1) {
				$result1 = 0;
			} else {
				$result1 = 11 - $sum;
			}
			if ($result1 == $number [13]) {
				$sum = $number [1] * 6 + $number [2] * 5 + $number [3] * 4 + $number [4] * 3 + $number [5] * 2 + $number [6] * 9 + $number [7] * 8 + $number [8] * 7 + $number [9] * 6 + $number [10] * 5 + $number [11] * 4 + $number [12] * 3 + $number [13] * 2;
				$sum = $sum - (11 * (intval ( $sum / 11 )));
				if ($sum == 0 || $sum == 1) {
					$result2 = 0;
				} else {
					$result2 = 11 - $sum;
				}
				if ($result2 == $number [14]) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Check if this is a valid email
	 *
	 * @param $email string        	
	 * @return boolean
	 */
	public static function validateEmail($email) {
		if (! strstr ( $email, "@" )) {
			return false;
		} else {
			list ( $user, $host ) = explode ( "@", $email );
			return fsockopen ( "www." . $host, 80, $errno, $errstr, 5 ) == true;
		}
	}
	
	/**
	 * To check if an url exists
	 *
	 * @param $url string        	
	 * @return boolean
	 */
	public static function urlExists($url) {
		$headers = get_headers ( $url, true );
		$response = $headers [0];
		
		return strpos ( $response, "200 OK" ) !== false;
	}
	
	/**
	 * TO Redir
	 *
	 * @param $url string        	
	 */
	public static function redir($url) {
		header ( 'Location: ' . $url, true );
		exit ();
	}
	
	/**
	 * To Send SMS
	 *
	 * @param $to string        	
	 * @param $msg string        	
	 */
	public static function sendSms($to, $msg) {
		$result = false;
		// No Send SMS Messages when website are on depuration mode (Limit
		// Resources)
		if (! DEBUG) {
			$msg = urlencode ( utf8_decode ( $msg ) );
			$url = "https://system.human.com.br/GatewayIntegration/msgSms.do?dispatch=send";
			$url .= "&account=" . self::$sms_configs ['account'];
			$url .= "&code=" . self::$sms_configs ['code'];
			$url .= "&from=" . self::$sms_configs ['from'];
			$url .= "&to=" . $to;
			$url .= "&msg=" . $msg;
			
			$result = file_get_contents ( $url );
		} else {
			// Say to developer (Hey Buddy, you can't send a real message now,
			// Your are Developing it Yeat!)
			$result = "999 - Application on depuration mode, message not really sent";
		}
		return $result;
	}
	
	/**
	 * Check if needs Login
	 */
	public static function authCheck() {
		if (find ( 'panel', CONTROLLER ) && CONTROLLER != 'panel/login' && CONTROLLER != 'panel/logout' && CONTROLLER != 'panel/forgot_password' && CONTROLLER != 'panel/forgot_password_change' && CONTROLLER != 'panel/unlock_account' && CONTROLLER != 'panel/resend_authorization' && CONTROLLER != 'panel/saveBookmark') {
			if (! self::userIsLogged ()) {
				$_SESSION ['SYSTEM'] ['REDIR'] = $_SERVER ['REQUEST_URI'];
				self::redir ( BASE_DIR . '/panel/login' );
			}
		}
	}
	public static function logout() {
		if (self::userIsLogged ()) {
			unset ( $_SESSION ['SYSTEM'] ['RESTRICT'], $_SESSION ['USER'] );
		}
	}
	
	/**
	 * To Auth User
	 *
	 * @param $id integer        	
	 */
	public static function auth($id) {
		$account = self::$db->find ( 'vw_accounts', array (), array ( 'id' => $id ) );
		$account = $account [0];
		
		$plan = self::$db->find ( 'plans', array (), array ( 'id' => $account ['plan_id'] ) );
		$plan = $plan [0];
		
		unset ( $_SESSION ['SYSTEM'] ['RESTRICT'] ['LOGGED'], $_SESSION ['USER'] );
		
		$_SESSION ['SYSTEM'] ['RESTRICT'] ['LOGGED'] = true;
		$_SESSION ['USER'] = $account;
		$_SESSION ['USER'] ['PLAN'] = $plan;
		return self::$db->save ( array ( 'id' => $id, 'login_attemps' => 0, 'accesses' => ($account ['accesses'] + 1), 'last_access' => date ( "Y-m-d H:i:s" ) ), 'accounts', array ('id' => $id) );
	}
	
	/**
	 * To get all owner resources (Plan)
	 *
	 * @param $id integer        	
	 */
	public static function getOwnerResources($id) {
		$data = self::$db->find ( 'accounts', array (), array (	'id' => $id ) );
		$data = $data [0];
		$plan = self::$db->find ( 'plans', array (), array ( 'id' => $data ['plan_id'] ) );
		$plan = $plan [0];
		return $plan;
	}
	
	/**
	 * Test if a file exists and if are not empty
	 *
	 * @param $file string        	
	 * @return boolean
	 */
	public static function testFile($file) {
		$result = false;
		if (file_exists ( $file )) {
			if (is_readable ( $file )) {
				if (filesize ( $file ) > 0) {
					$result = true;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * delete a list of files
	 *
	 * @param $list array        	
	 */
	public static function unlinkArray(array $list) {
		foreach ( $list as $file ) {
			if (! empty ( $file )) {
				if (file_exists ( $file )) {
					@unlink ( $file );
				}
			}
		}
	}
	
	/**
	 * Check if the current user was been logged
	 *
	 * @return boolean
	 */
	public static function userIsLogged() {
		return isset ( $_SESSION ['SYSTEM'] ['RESTRICT'] ['LOGGED'], $_SESSION ['USER'] );
	}
	
	/**
	 * To redir by System Session
	 */
	public static function SystemRedir() {
		if (isset ( $_SESSION ['SYSTEM'] ['REDIR'] )) {
			$redir = $_SESSION ['SYSTEM'] ['REDIR'];
			unset ( $_SESSION ['SYSTEM'] ['REDIR'] );
			tools::redir ( $redir );
		}
	}
	
	/**
	 * To remove a complete folder
	 *
	 * @param $dir string        	
	 */
	public static function removeDir($dir) {
		$files = glob ( $dir . '*', GLOB_MARK );
		foreach ( $files as $file ) {
			if (substr ( $file, - 1 ) == '/') {
				self::removeDir ( $file );
			} else {
				unlink ( $file );
			}
		}
		
		if (is_dir ( $dir )) {
			rmdir ( $dir );
		}
	}
	public static function folder($path) {
		if (! file_exists ( $path )) {
			if (! is_dir ( $path )) {
				mkdir ( $path, 0777, true );
			}
		}
		return $path;
	}
	
	/**
	 * To count script generation time
	 *
	 * @param $param interger        	
	 * @param $starttime integer        	
	 */
	public static function timer($param, $starttime = 0) {
		$result = null;
		if ($param === 1) {
			$mtime = microtime ();
			$mtime = explode ( " ", $mtime );
			$mtime = $mtime [1] + $mtime [0];
			$starttime = $mtime;
			$result = $starttime;
		} elseif ($param === 2) {
			$mtime = microtime ();
			$mtime = explode ( " ", $mtime );
			$mtime = $mtime [1] + $mtime [0];
			$endtime = $mtime; // Finaliza a variável de contagem do tempo de
			                   // geração da página.
			$totaltime = ($endtime - $starttime);
			$result = round ( $totaltime, 2 );
		}
		
		return $result;
	}
	
	/**
	 * Request a file from Library
	 *
	 * @param $class string        	
	 */
	public static function Library($class) {
		require_once (LIBRARY_DIR . '/' . $class . '.php');
	}
	
	/**
	 * Request a file from Configs
	 *
	 * @param $file string        	
	 */
	public static function Configs($file) {
		global $CONFIGS;
		require (CONFIGS_DIR . '/' . $file . '.php');
	}
	
	/**
	 * To Truncate an Text
	 *
	 * @param $txt string        	
	 * @param $max integer        	
	 */
	public static function truncateText($txt, $max, $end = '...') {
		$txtSize = strlen ( $txt );
		if ($txtSize >= $max) {
			$txt = substr ( $txt, 0, $max - strlen ( $end ) );
			$txt .= $end;
		}
		return $txt;
	}
	
	/**
	 * To generate an sms message
	 *
	 * @param $file string        	
	 * @param $vars array        	
	 * @return string
	 */
	public static function getSmsMsg($file, array $vars = array()) {
		$txt = file_get_contents ( VIEWS_DIR . '/sms/' . $file . '.txt' );
		foreach ( $vars as $key => $val ) {
			$txt = str_replace ( '[' . $key . ']', $val, $txt );
		}
		return $txt;
	}
	
	/**
	 * Add Space Between Text
	 *
	 * @param $text string        	
	 * @return string
	 */
	public static function addSpaceText($text) {
		$newText = '';
		$n = strlen ( $text );
		for($i = 0; $i < $n; $i ++) {
			$newText .= $text {$i} . ' ';
		}
		return $newText;
	}
	public static function captchaImg($from = '') {
		echo captcha::generateHTML($from);	
	}
	
	/**
	 * Encrypt a text (Encrypting 3 times with 2 keys by RIJNDAEL 256 bit
	 * Cryptographic, messing the text a loot! and coding the text a loot too!
	 * and converting and reconverting): With that you protect the text and
	 * leaving the text 470% larger than the original
	 *
	 * @author Júlio César <julio@juliocesar.me>
	 * @param $str string        	
	 * @param $key string        	
	 * @return string
	 */
	public static function encrypt($str, $key1, $key2) {
		// Get MD5 SHA1 Keys HASH
		$key1 = md5 ( sha1 ( $key1 ) );
		$key2 = md5 ( sha1 ( $key2 ) );
		
		// Encrypt String
		$encrypted = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $key1, $str, MCRYPT_MODE_CFB, $key2 );
		// Compress and mess the encrypted text
		$encrypted = gzcompress ( $encrypted, 9 );
		// Format to a readable text
		$encrypted = convert_uuencode ( $encrypted );
		// Encrypt again by interting the keys position
		$encrypted = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $key2, $encrypted, MCRYPT_MODE_CFB, $key1 );
		// Format to a readable text
		$encrypted = base64_encode ( $encrypted );
		// Encrypt one more time with the 2 keys concatenaed by md5 hash
		$encrypted = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, md5 ( $key1 . $key2 ), $encrypted, MCRYPT_MODE_CFB, md5 ( $key2 . $key1 ) );
		// Convert the Binary Encryted code to Hexa code (Readable text)
		$encrypted = bin2hex ( $encrypted );
		// Revert string
		$encrypted = strrev ( $encrypted );
		return $encrypted;
	}
	
	/**
	 * Decrypt a text encrypted by tools::encrypt function Decrypting 3 times
	 * with 2 keys by RIJNDAEL 256 bit Cryptographic, unmessing the text a loot!
	 * and uncoding the text a loot too! and unconverting): With that you
	 * unprotect the text and leaving the text 470% smaller than the "original"
	 * (Encrypted text) but EXACLY THE SAME as the ORIGINAL TEXT
	 *
	 * @author Júlio César <julio@juliocesar.me>
	 * @param $str string        	
	 * @param $key string        	
	 * @return string
	 */
	public static function decrypt($str, $key1, $key2) {
		// Get MD5 Sha1 Keys HASH
		$key1 = md5 ( sha1 ( $key1 ) );
		$key2 = md5 ( sha1 ( $key2 ) );
		
		// UnRevert string
		$decrypted = strrev ( $str );
		// Convert hexa code to original binary code
		$decrypted = hex2bin ( $decrypted );
		// Uncrypt by 2 keys concatenaed by md5 hash
		$decrypted = mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, md5 ( $key1 . $key2 ), $decrypted, MCRYPT_MODE_CFB, md5 ( $key2 . $key1 ) );
		// Unformat from readable text
		$decrypted = base64_decode ( $decrypted );
		// Uncrypt by interting the keys position
		$decrypted = mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $key2, $decrypted, MCRYPT_MODE_CFB, $key1 );
		// UnFormat from a readable text
		$decrypted = convert_uudecode ( $decrypted );
		// Uncompress and unmess the text
		$decrypted = gzuncompress ( $decrypted );
		// Uncrypt the text
		$decrypted = mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $key1, $decrypted, MCRYPT_MODE_CFB, $key2 );
		// Remove the ODD spaces and make it clean!
		$decrypted = trim ( $decrypted );
		return $decrypted;
	}
	
	/**
	 * Generate a redirect ID
	 *
	 * @param $url string        	
	 * @return interger
	 */
	public static function genRedirID($url) {
		$data = self::$db->find ( 'redirects', array (), array ( 'url' => $url ) );
		if (empty ( $data )) {
			self::$db->save ( array ( 'url' => $url	), 'redirects' );
			$id = self::$db->getLastID ();
		} else {
			$data = $data [0];
			$id = $data ['id'];
		}
		
		return $id;
	}
	
	/**
	 * Generate a saldation message relative of hour of day
	 */
	public static function saldation() {
		$h = ( int ) date ( "H" );
		if ($h >= 0 && $h <= 4) {
			$text = "Boa Madrugada";
		} elseif ($h >= 5 && $h <= 11) {
			$text = "Bom Dia";
		} elseif ($h >= 12 && $h <= 18) {
			$text = "Boa Tarde";
		} elseif ($h >= 19 && $h <= 23) {
			$text = "Boa Noite";
		}
		echo $text;
	}
	
	/**
	 * Slug function
	 *
	 * @param $str string        	
	 * @return string
	 */
	public static function slug($str) {
		// Convert UTF-8 to ASCII-TRANSLIT
		$str = iconv ( CHARSET, 'ASCII//TRANSLIT', $str );
		// Convert str to lowerCase
		$str = strtolower ( $str );
		// Replace spaces with -
		$str = str_replace ( " ", "-", $str );
		// Remove all characters that is not a-z and -
		$str = preg_replace ( '/[^a-z,0-9,-]/', '', $str );
		
		return $str;
	}
	
	/**
	 * Format hours
	 * 
	 * @param integer $hours        	
	 * @return string
	 */
	public static function formatHours($hours) {
		$result = '';
		
		if ($hours == 1) {
			$result = '1 hora';
		} elseif ($hours < 24 && $hours > 1) {
			$result .= $hours . ' horas';
		}
		if ($hours >= 24 && $hours < 48) {
			$result = '1 dia';
		} elseif ($hours >= 24 && $hours < 168) {
			$result .= round ( $hours / 24 ) . ' dias';
		}
		if ($hours >= 168 && $hours < 336) {
			$result = '1 semana';
		} elseif ($hours > 168 && $hours < 672) {
			$result .= round ( $hours / 168 ) . ' semanas';
		}
		if ($hours >= 672 && $hours < 1344) {
			$result = '1 mês';
		} elseif ($hours >= 672 && $hours < 8064) {
			$result .= round ( $hours / 672 ) . ' meses';
		}
		if ($hours >= 8064 && $hours < 16128) {
			$result = '1 ano';
		} elseif ($hours >= 16128) {
			$result .= round ( $hours / 8064 ) . ' anos';
		}
		
		return $result;
	}
	
	/**
	 * Phone Encode
	 * Safe to Lammers!
	 *
	 * @param $phone string        	
	 * @return string
	 */
	public static function phoneEncode($phone) {
		// Compress and mess the encrypted text
		$result = gzcompress ( $phone, 9 );
		// Format to a readable text
		$result = convert_uuencode ( $result );
		// Format to hex from Binary
		$result = bin2hex ( $result );
		
		return $result;
	}
	
	/**
	 * Phone Decode
	 * Safe to Lammers!
	 *
	 * @param $phone string        	
	 * @return string
	 */
	public static function phoneDecode($phone) {
		$result = preg_replace ( '/[^a-z,0-9]/', '', $phone );
		// Format to hex from Binary
		$result = @hex2bin ( $result );
		// Format to a readable text
		$result = @convert_uudecode ( $result );
		// Compress and mess the encrypted text
		$result = @gzuncompress ( $result );
		
		return $result;
	}
	
	/**
	 * Calculate the time between 2 Dates
	 * 
	 * @param multitype:string|integer $date1        	
	 * @param multitype:string|integer $date2        	
	 * @return multitype:number boolean
	 */
	public static function time2dates($date1, $date2) {
		$date1 = is_int ( $date1 ) ? $date1 : strtotime ( $date1 );
		$date2 = is_int ( $date2 ) ? $date2 : strtotime ( $date2 );
		
		if (($date1 !== false) && ($date2 !== false)) {
			if ($date2 >= $date1) {
				$diff = ($date2 - $date1);
				
				if ($days = intval ( (floor ( $diff / 86400 )) )) {
					$diff %= 86400;
				}
				if ($hours = intval ( (floor ( $diff / 3600 )) )) {
					$diff %= 3600;
				}
				if ($minutes = intval ( (floor ( $diff / 60 )) )) {
					$diff %= 60;
				}
				
				return array (
						$days,
						$hours,
						$minutes,
						intval ( $diff ) 
				);
			}
		}
		
		return false;
	}
	
	/**
	 * Register or Log in the Visitor
	 * 
	 * @return integer
	 */
	public static function visitorRegister() {
		// Set Failsafe ID
		$id = 0;
		
		// Check if user is Registring
		if (!empty ( $_POST ['email'] )) {
			// Get E-mail
			$email = $_POST['email'];
			// Reset Visitor Account
			$_SESSION ['TEMP'] ['ACCOUNT'] = array ();
			
			// Check if Visitor is Already Registred
			$check = self::$db->find ( 'visitor_accounts', array (), array ( 'email' => $email ) );
			
			// Check if Visitor is New
			if ( empty ( $check )) {
				if (self::$db->save ( $_POST, 'visitor_accounts' )) {
					$data = self::$db->find ( 'visitor_accounts', array (), array ( 'email' => $email	) );
					if (! empty ( $data )) {
						$_SESSION ['TEMP'] ['ACCOUNT'] = $data[0];
					}
				}
			} else {
				$_SESSION ['TEMP'] ['ACCOUNT'] = $check [0];
			}			
		}
		
		$id = $_SESSION ['TEMP'] ['ACCOUNT'] ['id'];
		
		return $id;
	}
}
?>