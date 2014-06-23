<?php 

namespace Database;

class PDODatabase implements DatabaseI {
	private static $dbProgram = "sqlite3";
	private static $dbDriver = "sqlite";
	private static $dbFile = "./data/database.sqlite";

	private $pdo = NULL;

	public function __construct() {
		$dsn = PDODatabase::$dbDriver . ":" . PDODatabase::$dbFile;
		$pdo = new \PDO($dsn);
		$pdo->exec("PRAGMA foreign_keys=ON");
		$this->pdo = $pdo;
	}

	public function startTakingRequests() {
		$this->pdo->beginTransaction();
	}
	public function executeAllRequests() {
		$this->pdo->commit();
	}
	public function forgetAllRequests() {
		$this->pdo->rollBack();
	}

	public function userExists($username) {
		try {
			$pdoStatement = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
			$pdoStatement->execute(array($username));
			$result = $pdoStatement->fetchColumn(0);
			return $result > 0;
		}
		catch (\Exception $ex) {
			error_log("Error checking user ({$username}) existance: " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return -1;
		}
	}

	public function createUser($username) {
		try {
			$result = $this->pdo->query("SELECT root FROM users ORDER BY root DESC LIMIT 1");
			$root = $result->fetchColumn(0);
			$root += 1;

			$pdoStatement = $this->pdo->prepare("INSERT INTO users (username, root) VALUES (?, ?)");
			if ($pdoStatement->execute(array($username, $root))) {
				return $root;
			}
			else {
				$errorInfo = $pdoStatement->errorInfo();
				error_log("Unable to create user: " . $errorInfo[2]);
				return false;
			}
		}
		catch (\Exception $ex) {
			error_log("Unable to create user ({$username}): " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return false;
		}
	}
	public function getUserRoot($username) {
		try {
			$pdoStatement = $this->pdo->prepare("SELECT root FROM users WHERE username = ?");
			$pdoStatement->execute(array($username));
			return $pdoStatement->fetchColumn(0);
		}
		catch (\Exception $ex) {
			error_log("Unable to get user root ({$username}): " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return "";
		}
	}

	public function getAllProjects($username) {
		try {
			$pdoStatement = $this->pdo->prepare("SELECT * FROM projects WHERE owner = ?");
			$pdoStatement->execute(array($username));
			return $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
		}
		catch (\Exception $ex) {
			error_log("Unable to get all projects: " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return array();
		}
	}

	public function createProject($username, $projectName) {
		try {

			$usersProjects = $this->getAllProjects($username);
			foreach ($usersProjects as $project) {
				if ($projectName == $project['name']) {
					error_log("Unable to create project: User has already used that name.");
					return false;
				}
			}

			$pdoStatement = $this->pdo->prepare("SELECT id FROM projects WHERE owner = ? ORDER BY id DESC LIMIT 1");
			$pdoStatement->execute(array($username));
			$id = $pdoStatement->fetchColumn(0);
			$id += 1;

			$pdoStatement = $this->pdo->prepare("INSERT INTO projects (id, owner, name) VALUES (:id, :owner, :name)"); 
			$result = $pdoStatement->execute(array(
				"id" => "$id",
				"owner" => $username,
				"name" => $projectName,
			));
			if ($result) {
				return $id;
			}
			else {
				$errorInfo = $pdoStatement->errorInfo();
				error_log("Unable to create project: " . $errorInfo[2]);
				return false;
			}
		}
		catch (\Exception $ex) {
			error_log("Unable to create project: " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return false;
		}
	}

	public function getProjectName($username, $projectId) {
		try {
			$pdoStatement = $this->pdo->prepare("SELECT name FROM projects WHERE owner = :name AND id = :id");
			$pdoStatement->execute(array ("name" => $username, "id" => $projectId));
			$result = $pdoStatement->fetchColumn(0);
			if ($result) {
				return $result;
			}
			else {
				return "ERROR";
			}
		}
		catch (\Exception $ex) {
			error_log("Unable to find project name: " . $ex->getMessage());
			// TODO error hendling
			return "ERROR";
		}
	}

	public function createUploadedFile($username, $projectId, $fileName, $fileType, $isDownload = false, $size = -1) {
		try {
			$pdoStatement = $this->pdo->prepare("INSERT INTO uploaded_files (project_id, project_owner, name, file_type, status, approx_size)
				VALUES (:id, :owner, :name, :fileType, :status, :size)");
			$status = ($isDownload) ? 1 : 0;
			$insertSuccess = $pdoStatement->execute(array("owner" => $username, "id" => $projectId, "name" => $fileName,
				"fileType" => $fileType, "status" => $status, "size" => $size));
			if (!$insertSuccess) {
				$errorInfo = $pdoStatement->errorInfo();
				throw new \PDOException("Unable to insert uploaded_file: " . $errorInfo[2]);
			}
			return true;
		}
		catch (\Exception $ex) {
			error_log("Unable to create uploaded file: " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return false;
		}
	}

	public function getAllUploadedFiles($username, $projectId) {
		$files = array();
		try {
			$pdoStatement = $this->pdo->prepare("SELECT name, file_type, description, approx_size FROM uploaded_files
				INNER JOIN file_statuses ON uploaded_files.status = file_statuses.status
				WHERE project_id = :id AND project_owner = :owner");
			$pdoStatement->execute(array("id" => $projectId, "owner" => $username));
			$files = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
		}
		catch (\Exception $ex) {
			error_log("Unable to get uploaded files: " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
		}
		return $files;
	
	}

	public function removeUploadedFile($username, $projectId, $fileName) {
		try {
			$pdoStatement = $this->pdo->prepare("DELETE FROM uploaded_files WHERE project_id = :id AND project_owner = :owner AND name = :name");
			return $pdoStatement->execute(array("id" => $projectId, "owner" => $username, "name" => $fileName));
		}
		catch (\Exception $ex) {
			error_log("Unable to delete file: " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return false;
		}
	}

	public function saveRun($username, $projectId, $scriptName, $scriptText) {
		try {
			$pdoStatement = $this->pdo->prepare("INSERT INTO script_runs (project_owner, project_id, script_name, script_string)
				VALUES (:owner, :id, :name, :string)");
			$result = $pdoStatement->execute(array("owner" => $username, "id" => $projectId, "name" => $scriptName, "string" => $scriptText));
			if ($result) {
				return $this->pdo->lastInsertId();
			}
			else {
				return false;
			}
		}
		catch (\Exception $ex) {
			error_log("Unable to save run: " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return false;
		}
	}

	public function addRunResults($runId, $consoleOutput, $version) {
		try {
			$pdoStatement = $this->pdo->prepare("UPDATE script_runs SET output = :output, version = :version WHERE id = :id");
			return $pdoStatement->execute(array("id" => $runId, "output" => $consoleOutput, "version" => $version));
		}
		catch (\Exception $ex) {
			error_log("Unable to add results from run: " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return false;
		}
	}

	public function getPastRuns($username, $projectId) {
		try {
			$pdoStatement = $this->pdo->prepare("SELECT * FROM script_runs WHERE project_owner = :owner AND project_id = :id");
			$pdoStatement->execute(array("owner" => $username, "id" => $projectId));
			$result = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
			if ($result === FALSE) {
				$errorInfo = $pdoStatement->errorInfo();
				error_log("Unable to retrieve past runs: " . $errorInfo[2]);
				return array();
			}
			return $result;
		}
		catch (\Exception $ex) {
			error_log("Unable to retrieve past runs: " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return array();
		}
	}

	public function renderCommandUploadSuccess($username, $projectId, $fileName, $size) {
		if (!$this->uploadExists($username, $projectId, $fileName)) {
			throw new \Exception("File not found");
		}
		return $this->renderCommandUpdateFile($username, $projectId, $fileName, 0, $size);
	}
	public function renderCommandUploadFailure($username, $projectId, $fileName, $size) {
		if (!$this->uploadExists($username, $projectId, $fileName)) {
			throw new \Exception("File not found");
		}
		return $this->renderCommandUpdateFile($username, $projectId, $fileName, 2, $size);
	}
	private function renderCommandUpdateFile($username, $projectId, $fileName, $status, $size) {
		$sql = "UPDATE uploaded_files SET status = {$status}, approx_size = " . $this->pdo->quote($size) . 
		   	" WHERE project_owner = " . $this->pdo->quote($username) . 
			" AND project_id = " . $this->pdo->quote($projectId) . 
			" AND name = " . $this->pdo->quote($fileName) . ";";
		$command = PDODatabase::$dbProgram. " " . PDODatabase::$dbFile . " \"" . preg_replace("/\"/", "\\\"", $sql) . "\"";
		return $command;		
	}
	public function uploadExists($username, $projectId, $fileName) {
		try {
			$pdoStatement = $this->pdo->prepare("SELECT COUNT(*) FROM uploaded_files WHERE
				project_owner = :owner AND project_id = :id AND name = :fileName");
			$pdoStatement->execute(array("owner" => $username, "id" => $projectId, "fileName" => $fileName));
			$fileCount = $pdoStatement->fetchColumn(0);
			$pdoStatement->closeCursor();
			return $fileCount == 1;
		}
		catch (\PDOException $ex) {
			error_log($ex->getMessage());
			return false;
		}
	}

	public function changeFileName($username, $projectId, $fileName, $newFileName) {
		try {
			$pdoStatement = $this->pdo->prepare("UPDATE uploaded_files SET name = :newName WHERE 
				project_owner = :owner AND project_id = :id AND name = :oldName");
			return $pdoStatement->execute(array("owner" => $username, "id" => $projectId, 
				"newName" => $newFileName, "oldName" => $fileName));
		}
		catch (\Exception $ex) {
			error_log("Unable to : " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
			return false;
		}
	}

	/*Try catch block commong to all functions
		try {
		}
		catch (\Exception $ex) {
			error_log("Unable to : " . $ex->getMessage());
			// TODO error handling
			// TODO transactions
		}
	 */
}
