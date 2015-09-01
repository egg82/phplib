<?php
	include_once(dirname(__FILE__)."/util.php");
	include_once(dirname(__FILE__)."/database.php");
	include_once(dirname(__FILE__)."/security.php");
	
	class Autologin {
		public function __construct() {
			global $util, $database, $security;
			
			if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["priv"])) {
				$_SESSION["user_id"] = 0;
				$_SESSION["username"] = "";
				$_SESSION["priv"] = 0;
			}
	
			if ($_SESSION["user_id"] == 0 && isset($_COOKIE["user"]) && isset($_COOKIE["encrypted_pass"])) {
				$user = $database->sanitize($util->sanitize($_COOKIE["user"]));
				$result = $database->query("SELECT `username`, `id`, `pass_hash`, `key`, `iv`, `priv` FROM `users` WHERE `username`=".$user." OR `email`=".$user.";");
		
				$cookie_user = $_COOKIE["user"];
				$cookie_encrypted_pass = $_COOKIE["encrypted_pass"];
		
				$security->delete_cookie("user");
				$security->delete_cookie("encrypted_pass");
		
				if (count($result) == 1 && password_verify($security->decrypt($cookie_encrypted_pass, $result[0]["key"], base64_decode($result[0]["iv"])), $result[0]["pass_hash"])) {
					$_SESSION["user_id"] = $result[0]["id"];
					$_SESSION["username"] = $result[0]["username"];
					$_SESSION["priv"] = $result[0]["priv"];
			
					$security->set_cookie("user", $cookie_user);
					$security->set_cookie("encrypted_pass", $cookie_encrypted_pass);
				}
			}
		}
	}
	
	$autologin = new Autologin();
?>
