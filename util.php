<?php
	include_once(dirname(__FILE__)."/conf.php");
	
	class Util {
		public function __construct() {
			
		}
		
		public function die503() {
			header('HTTP/1.1 503 Service Temporarily Unavailable');
			header('Status: 503 Service Temporarily Unavailable');
			header('Retry-After: 300');
			die();
		}
		
		public function sanitize($string) {
			return htmlspecialchars($string, ENT_QUOTES);
		}
		
		public function format_date($date, $format) {
			$t = strtotime($date);
			return date($format, $t);
		}
		
		public function random_string($length) {
			global $conf;
			
			$char_len = strlen($conf->rand_chars);
			$rand_string = '';
			
			for ($i = 0; $i < $length; $i++) {
				$rand_string .= $conf->rand_chars[mt_rand(0, $char_len - 1)];
			}
			
			return $rand_string;
		}

		public function unicode_unescape($str) {
			return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($match) {
				return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
			}, $str);
		}
	}
	
	$util = new Util();
?>
