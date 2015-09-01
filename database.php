<?php
	include_once(dirname(__FILE__)."/conf.php");
	include_once(dirname(__FILE__)."/logger.php");
	include_once(dirname(__FILE__)."/util.php");
	
	class Database {
		private $connection = null;
		
		public function __construct($type) {
			global $conf, $logger, $util;
			
			try {
				if ($type == "mysql") {
					$this->connection = new \PDO("mysql:host=".$conf->db_host.";dbname=".$conf->db_name, $conf->db_user, $conf->db_pass,
						array(
							\PDO::ATTR_PERSISTENT => true
						)
					);
				} else if ($type == "mssql" || $type == "sybase") {
					$this->connection = new \PDO($type.":host=".$conf->db_host.";dbname=".$conf->db_name.", ".$conf->db_user.", ".$conf->db_pass,
						array(
							\PDO::ATTR_PERSISTENT => true
						)
					);
				} else if ($type == "sqlite") {
					$this->connection = new \PDO("sqlite:".$conf->db_name,
						array(
							\PDO::ATTR_PERSISTENT => true
						)
					);
				} else {
					$logger->log("PDO", "ERROR", "Database type not supported.");
					return;
				}
				
				$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			} catch (\PDOException $ex) {
				$logger->log("PDO", "CRITICAL", $ex);
				$util->die503();
			}

			$this->execute("set character_set_client='utf8';set character_set_results='utf8';set collation_connection='utf8_bin';");
		}
		
		public function query($q) {
			global $logger;
			
			try {
				$statement = $this->connection->prepare($q);
				$statement->setFetchMode(\PDO::FETCH_BOTH);
				$statement->execute();
				
				$retarr = array();
				while($row = $statement->fetch()) {
					array_push($retarr, $row);
				}
				
				return $retarr;
			} catch (\PDOException $ex) {
				$logger->log("PDO", "ERROR", $ex);
				return null;
			}
		}
		public function execute($q) {
			global $logger;
			
			try {
				$statement = $this->connection->prepare($q);
				$statement->setFetchMode(\PDO::FETCH_BOTH);
				return $statement->execute();
			} catch (\PDOException $ex) {
				$logger->log("PDO", "ERROR", $ex);
				return false;
			}
		}
		public function sanitize($string) {
			return $this->connection->quote($string);
		}
		public function getLastInsertId() {
			return $this->connection->lastInsertId();
		}
	}
	
	$database = new Database($conf->db_type);
?>
