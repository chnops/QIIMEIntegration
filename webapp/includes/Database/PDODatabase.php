<?php 

namespace Database;

class PDODatabase implements DatabaseI {
	private static $dsn = "sqlite:./data/database.sqlite";

	private $operatingSystem = NULL;
	private $pdo = NULL;

	public function __construct(\Models\OperatingSystemI $operatingSystem) {
		$pdo = new \PDO(PDODatabase::$dsn);
		$pdo->exec("PRAGMA foreign_keys=ON");
		$this->pdo = $pdo;
		$this->operatingSystem = $operatingSystem;
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
			$this->pdo->beginTransaction();
			$result = $this->pdo->query("SELECT root FROM users ORDER BY root DESC LIMIT 1");
			$root = $result->fetchColumn(0);
			$root += 1;

			$pdoStatement = $this->pdo->prepare("INSERT INTO users (username, root) VALUES (?, ?)");
			if ($pdoStatement->execute(array($username, $root))) {

				// TODO this functionality should be stored elsewhere, for example, the Roster object.
				// Once this is accomplished, though, the controllers won't even need their DatabaseI object.
				$this->operatingSystem->createDir('u' . $root);
				$this->pdo->commit();
				return $root;
			}
			else {
				$errorInfo = $pdoStatement->errorInfo();
				error_log("Unable to create user: " . $errorInfo[2]);
				$this->pdo->rollBack();
				return false;
			}
		}
		catch (\Exception $ex) {
			$this->pdo->rollBack();
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

	public function createUploadedFile($username, $projectId, $fileName, $fileType) {
		try {
			$pdoStatement = $this->pdo->prepare("INSERT INTO uploaded_files (project_id, project_owner, name, file_type)
				VALUES (:id, :owner, :name, :fileType)");
			$insertSuccess = $pdoStatement->execute(array("owner" => $username, "id" => $projectId, "name" => $fileName, "fileType" => $fileType));
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
			$pdoStatement = $this->pdo->prepare("SELECT * FROM uploaded_files WHERE project_id = :id AND project_owner = :owner");
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
