<?php 

namespace Database;

class PDODatabase implements DatabaseI {

	private static $dsn = "sqlite:data/database.sqlite";
	private $pdo = NULL;

	public function __construct() {
		$pdo = new \PDO(PDODatabase::$dsn);
		$pdo->exec("PRAGMA foreign_keys=ON");
		$this->pdo = $pdo;
	}

	public function userExists($username) {
		try {
			$pdoStatement = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
			if ($pdoStatement === false) {
				$errorInfo = $this->pdo->errorInfo();
				throw new \PDOException("Unable to prepare statement: {$errorInfo[2]}");
			}
			$pdoStatement->execute(array($username));
			$result = $pdoStatement->fetchColumn(0);
			return $result > 0;
		}
		catch (\PDOException $ex) {
			error_log("Error checking user ({$username}) existance: " . $ex->getMessage());
			// TODO error handling
			return false;
		}
	}

	public function insertUser($username, $root) {
		try {
			$pdoStatement = $this->pdo->prepare("INSERT INTO users (username, root) VALUES (?, ?)");
			if ($pdoStatement === false) {
				$errorInfo = $this->pdo->errorInfo();
				throw new \PDOException("Unable to prepare statement: {$errorInfo[2]}");
			}
			$pdoStatement->execute(array($username, $root));
		}
		catch (Exception $ex) {
			error_log("Unable to create user ({$username}): " . $ex->getMessage());
			// TODO error handling
		}
	}

	public function getHighestUserRoot() {
		try {
			$result = $this->pdo->query("SELECT root FROM users ORDER BY root DESC LIMIT 1");
			if ($result === FALSE) {
				$errorInfo = $this->pdo->errorInfo();
				throw new \PDOException("Unable to execute query: {$errorInfo[2]}");
			}
			return $result->fetchColumn(0);
		}
		catch (Exception $ex) {
			error_log("Unable to determine highest root: " . $ex);
			// TODO error handling
			return -1;
		}
	}
	public function getHighestProject($username) {
		try {
			$pdoStatement = $this->pdo->prepare("SELECT id FROM projects WHERE owner = ? ORDER BY id DESC LIMIT 1");
			if ($pdoStatement === FALSE) {
				$errorInfo = $this->pdo->errorInfo();
				throw new \PDOException("Unable to execute query: {$errorInfo[2]}");
			}
			$pdoStatement->execute(array($username));
			return $pdoStatement->fetchColumn(0);
		}
		catch (Exception $ex) {
			error_log("Unable to determine highest root: " . $ex);
			// TODO error handling
			return -1;
		}
	}

	public function getAllProjects($username) {
		try {
			$pdoStatement = $this->pdo->prepare("SELECT * FROM projects WHERE owner = ?");
			if ($pdoStatement === false) {
				$errorInfo = $this->pdo->errorInfo();
				throw new \PDOException("Unable to prepare statement: {$errorInfo[2]}");
			}
			$pdoStatement->execute(array($username));
			return $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
			}
		catch (\PDOException $ex) {
			error_log("Unable to get all projects: " . $ex->getMessage());
			// TODO error handling
			return array();
		}
	}
	public function createProject($username, $projectName) {
		// TODO may be -1...
		$id = $this->getHighestProject($username);
		try {
			$pdoStatement = $this->pdo->prepare("INSERT INTO projects (id, owner, name) VALUES (:id, :owner, :name)"); 
			$pdoStatement->execute(array(
				"id" => "$id",
				"owner" => $username,
				"name" => $projectName,
				));
		}
		catch (\PDOException $ex) {
			error_log("Unable to create project: " . $ex->getMessage());
			// TODO error handling
			return false;
		}
	}
}
