<?php

namespace Database;

class PDODatabaseTest extends \PHPUnit_Framework_TestCase {

	private $testDSN = "sqlite:./data/database.sqlite";
	private $pdo;

	private $userStatement = NULL;
	private $goodUser = array();

	private $projectStatement = NULL;
	private $goodProj = array();
	private $emptyProj = array();

	private $fileStatement = NULL;
	private $goodFile = array();
	private $downloadingFile = array();

	private $runStatement = NULL;
	private $completeRun = array();
	private $runningRun = array();

	private $runCommand = "command";
	private $runString = "command --arg='value'";
	private $runOutput = NULL;
	private $runVersion = NULL;
	private $runDefaultStatus = NULL;
	private $runDefaultDeleted = 0;

	private $newUser = array();
	private $newProj = array();
	private $newFile = array();

	private $object = NULL;

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->pdo = new \PDO($this->testDSN);

		$this->userStatement = $this->pdo->prepare("INSERT INTO users (username, root) VALUES (:username, :root)");
		$this->goodUser = array("username" => "asharp", "root" => 1);

		$this->projectStatement = $this->pdo->prepare("INSERT INTO projects (id, owner, name) VALUES (:id, :owner, :name)");
		$this->goodProj = array("owner" => $this->goodUser['username'], "id" => 1, "name" => "GoodProj");
		$this->emptyProj = array("owner" => $this->goodUser['username'], "id" => 2, "name" => "EmptyProj");

		$this->fileStatement = $this->pdo->prepare("INSERT INTO uploaded_files (project_id, project_owner, name, file_type, status, approx_size)
			VALUES (:project_id, :project_owner, :name, :file_type, :status, :approx_size)");
		$this->goodFile = array("project_id" => $this->goodProj['id'], "project_owner" => $this->goodProj['owner'],
			"name" => "File1.txt", "file_type" => "arbitrary_text", "status" => 0, "approx_size" => 88);
		$this->downloadingFile = array("project_id" => $this->goodProj['id'], "project_owner" => $this->goodProj['owner'],
			"name" => "File2.txt", "file_type" => "arbitrary_text", "status" => 1, "approx_size" => NULL);

		$this->runStatement = $this->pdo->prepare("INSERT INTO script_runs (id, project_id, project_owner, script_name, script_string, run_status)
			VALUES (:id, :project_id, :project_owner, :script_name, :script_string, :run_status)");
		$this->completeRun = array("id" => 1, "project_id" => $this->goodProj['id'], "project_owner" => $this->goodProj['owner'], "script_name" => $this->runCommand,
			"script_string" => $this->runString, "run_status" => -1);
		$this->runningRun = array("id" => 2, "project_id" => $this->goodProj['id'], "project_owner" => $this->goodProj['owner'], "script_name" => $this->runCommand,
			"script_string" => $this->runString, "run_status" => 78987);

		$this->newUser = array("username" => "asdfasdf", "root" => 2);
		$this->newProj = array("owner" => $this->goodUser['username'], "id" => 3, "name" => "NewProj");
		$this->newFile = array("project_id" => $this->goodProj['id'], "project_owner" => $this->goodProj['owner'],
			"name" => "New.txt", "file_type" => "arbitrary_text", "status" => 0, "approx_size" => 44);
	}

	public function setUp() {
		$this->object = new PDODatabase();

		$this->pdo->exec("DELETE FROM users");
		$this->userStatement->execute($this->goodUser);

		$this->pdo->exec("DELETE FROM projects");
		$this->projectStatement->execute($this->goodProj);
		$this->projectStatement->execute($this->emptyProj);
		
		$this->pdo->exec("DELETE FROM uploaded_files");
		$this->fileStatement->execute($this->goodFile);
		$this->fileStatement->execute($this->downloadingFile);
	
		$this->pdo->exec("DELETE FROM script_runs");
		$this->runStatement->execute($this->completeRun);
		$this->runStatement->execute($this->runningRun);
	}

	/**
	 * @covers PDODatabase::userExists
	 */
	public function testUserExists_userDoesExist() {
		$this->assertTrue($this->object->userExists($this->goodUser['username']));
	}
	/**
	 * @covers PDODatabase::userExists
	 */
	public function testUserExists_userDoesNotExist() {
		$this->assertFalse($this->object->userExists($this->newUser['username']));
	}

	/**
	 * @covers PDODatabase::createUser
	 */
	public function testCreateUser_userDoesNotExist() {
		$actuals = array();
		$expecteds = array(
			"function_return" => $this->newUser['root'],
			"new_database_row" => $this->newUser,
			"next_database_row" => false,
		);

		$actuals['function_return'] = $this->object->createUser($this->newUser['username']);

		$pdoStatement = $this->pdo->query("SELECT * FROM users WHERE username = \"{$this->newUser['username']}\"");
		$actuals['new_database_row'] = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
		$actuals['next_database_row'] = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers PDODatabase::createUser
	 */
	public function testCreateUser_userDoesExist() {
		$actuals = array();
		$expecteds = array(
			"function_return" => false
		);

		$actuals['function_return'] = $this->object->createUser($this->goodUser['username']);

		$this->assertSame($expecteds, $actuals);
	}

	/**
	 * @covers PDODatabase::getUserRoot
	 */
	public function testGetUserRoot_userDoesExist() {
		$expected = $this->goodUser['root'];
		
		$actual = $this->object->getUserRoot($this->goodUser['username']);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers PDODatabase::getUserRoot
	 */
	public function testGetUserRoot_userDoesNotExist() {
		$expected = false;
		
		$actual = $this->object->getUserRoot($this->newUser['username']);

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers PDODatabase::getAllProjects
	 */
	public function testGetAllProjects_userDoesNotExist() {

		$actual = $this->object->getAllProjects($this->newUser['username']);

		$this->assertEmpty($actual);
	}
	/**
	 * @covers PDODatabase::getAllProjects
	 */
	public function testGetAllProjects_userExistsAndHasProjects() {
		$expecteds = array($this->goodProj, $this->emptyProj);

		$actuals = $this->object->getAllProjects($this->goodUser['username']);

		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @depends testCreateUser_userDoesNotExist
	 * @covers PDODatabase::getAllProjects
	 */
	public function testGetAllProjects_userExistsButHasNoProjects() {
		$this->object->createUser($this->newUser['username']);

		$actuals = $this->object->getAllProjects($this->newUser['username']);

		$this->assertEmpty($actuals);
	}


	/**
	 * @covers PDODatabase::createProject
	 */
	public function testCreateProject_userDoesNotExist() {
		$actual = $this->object->createProject($this->newUser['username'], $this->newProj['name']);

		$this->assertFalse($actual);
	}
	/**
	 * @covers PDODatabase::createProject
	 */
	public function testCreateProject_userDoesExistAndProjectDoesNot() {
		$actuals = array();
		$expecteds = array(
			"function_return" => $this->newProj['id'],
			"new_database_row" => $this->newProj,
			"next_database_row" => false,
		);

		$actuals['function_return'] = $this->object->createProject($this->newProj['owner'], $this->newProj['name']);

		$pdoStatement = $this->pdo->query("SELECT * FROM projects WHERE owner = \"{$this->newProj['owner']}\" AND name = \"{$this->newProj['name']}\"");
		$actuals['new_database_row'] = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
		$actuals['next_database_row'] = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers PDODatabase::createProject
	 */
	public function testCreateProject_userDoesExistButSoDoesProject() {
		$expected = false;

		$actual = $this->object->createProject($this->goodUser['username'], $this->goodProj['name']);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers PDODatabase::getProjectName
	 */
	public function testGetProjectName_userDoesNotExist() {
		$expected = "ERROR";
	   
		$actual = $this->object->getProjectName($this->newUser['username'], $this->goodProj['id']);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers PDODatabase::getProjectName
	 */
	public function testGetProjectName_userDoesExistAndSoDoesProject() {
		$expected = $this->goodProj['name'];
		
		$actual = $this->object->getProjectName($this->goodProj['owner'], $this->goodProj['id']);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers PDODatabase::getProjectName
	 */
	public function testGetProjectName_userDoesExistButProjectDoesNot() {
		$expected = "ERROR";
		
		$actual = $this->object->getProjectName($this->newProj['owner'], $this->newProj['id']);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @coverse PDODatabase::createUploadedFile
	 */
	public function testCreateUploadedFile_userDoesNotExist() {
		$isDownload = false;
		$actuals = array();
		$expecteds = array(
			'function_return' => false,
			'row_count' => 0,
		);

		$actuals['function_return'] = $this->object->createUploadedFile($this->newUser['username'], $this->newFile['project_id'],
			$this->newFile['name'], $this->newFile['file_type'], $isDownload, $this->newFile['approx_size']);
		
		$pdoStatement = $this->pdo->query("SELECT COUNT(*) FROM uploaded_files WHERE name = \"{$this->newFile['name']}\"");
		$actuals['row_count'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @coverse PDODatabase::createUploadedFile
	 */
	public function testCreateUploadedFile_userDoesExistButProjectDoesNot() {
		$isDownload = false;
		$actuals = array();
		$expecteds = array(
			'function_return' => false,
			'row_count' => 0,
		);

		$actuals['function_return'] = $this->object->createUploadedFile($this->newFile['project_owner'], $this->newProj['id'],
			$this->newFile['name'], $this->newFile['file_type'], $isDownload, $this->newFile['approx_size']);
		
		$pdoStatement = $this->pdo->query("SELECT COUNT(*) FROM uploaded_files WHERE name = \"{$this->newFile['name']}\"");
		$actuals['row_count'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @coverse PDODatabase::createUploadedFile
	 */
	public function testCreateUploadedFile_fileDoesNotExist() {
		$isDownload = false;
		$actuals = array();
		$expecteds = array(
			'function_return' => true,
			'database_row' => $this->newFile,
		);

		$actuals['function_return'] = $this->object->createUploadedFile($this->newFile['project_owner'], $this->newFile['project_id'],
			$this->newFile['name'], $this->newFile['file_type'], $isDownload, $this->newFile['approx_size']);
		
		$pdoStatement = $this->pdo->query("SELECT * FROM uploaded_files WHERE name = \"{$this->newFile['name']}\"");
		$actuals['database_row'] = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @coverse PDODatabase::createUploadedFile
	 */
	public function testCreateUploadedFile_fileDoesExist() {
		$isDownload = false;
		$actuals = array();
		$expecteds = array(
			'function_return' => true, // TODO bad implementation
			'row_count' => 2, // TODO bad implementation
		);

		$actuals['function_return'] = $this->object->createUploadedFile($this->goodFile['project_owner'], $this->goodFile['project_id'],
			$this->goodFile['name'], $this->goodFile['file_type'], $isDownload, $this->goodFile['approx_size']);
		
		$pdoStatement = $this->pdo->query("SELECT COUNT(*) FROM uploaded_files WHERE name = \"{$this->goodFile['name']}\"");
		$actuals['row_count'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @coverse PDODatabase::createUploadedFile
	 */
	public function testCreateUploadedFile_downloadDoesNotExist() {
		$isDownload = true;
		$actuals = array();
		$expectedFile = $this->newFile;
		$expectedFile['status'] = 1;
		$expecteds = array(
			'function_output' => true,
			'new_database_row' => $expectedFile,
			'next_database_row' => false,
		);

		$actuals['function_output'] = $this->object->createUploadedFile($this->newFile['project_owner'], $this->newFile['project_id'],
			$this->newFile['name'], $this->newFile['file_type'], $isDownload, $this->newFile['approx_size']);

		$pdoStatement = $this->pdo->query("SELECT * FROM uploaded_files WHERE name = \"{$this->newFile['name']}\"");
		$actuals['new_database_row'] = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
		$actuals['next_database_row'] = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @coverse PDODatabase::createUploadedFile
	 * TODO bad implementation, should be separate function
	 */
	public function testCreateUploadedFile_downloadDoesExist() {
		$isDownload = true;
		$actuals = array();
		$expectedFile = $this->goodFile;
		$expectedFile['status'] = 1;
		$expecteds = array(
			'function_output' => true, // TODO bad implementation, should be false
			'row_count' => 2, // TODO bad implementation, should be 1
		);

		$actuals['function_output'] = $this->object->createUploadedFile($this->goodFile['project_owner'], $this->goodFile['project_id'],
			$this->goodFile['name'], $this->goodFile['file_type'], $isDownload, $this->goodFile['approx_size']);

		$pdoStatement = $this->pdo->query("SELECT COUNT(*) FROM uploaded_files WHERE name = \"{$this->goodFile['name']}\"");
		$actuals['row_count'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @coverse PDODatabase::getAllUploadedFiles
	 */
	public function testGetAllUploadedFiles_userDoesNotExist() { 
		$expected = array();

		$actual = $this->object->getAllUploadedFiles($this->newUser['username'], $this->goodProj['id']);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @coverse PDODatabase::getAllUploadedFiles
	 */
	public function testGetAllUploadedFiles_projectDoesNotExist() {
		$expected = array();

		$actual = $this->object->getAllUploadedFiles($this->goodUser['username'], $this->newProj['id']);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @coverse PDODatabase::getAllUploadedFiles
	 * TODO implementation: different file statuses
	 */
	public function testGetAllUploadedFiles_projectNotEmpty() {
		$expected = array($this->goodFile, $this->downloadingFile);
		foreach ($expected as &$file) {
			unset($file['project_owner']);
			unset($file['project_id']);
			if ($file['status'] == 1) {
				$file['description'] = "download in progress";
			}
			else {
				$file['description'] = "ready";
			}
			unset($file['status']);
		}

		$actual = $this->object->getAllUploadedFiles($this->goodProj['owner'], $this->goodProj['id']);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @coverse PDODatabase::getAllUploadedFiles
	 */
	public function testGetAllUploadedFiles_projectIsEmpty() {
		$expected = array();

		$actual = $this->object->getAllUploadedFiles($this->emptyProj['owner'], $this->emptyProj['id']);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @coverse PDODatabase::removeUploadedFile
	 */
	public function testRemoveUploadedFile_fileDoesExist() {
		$actuals = array();
		$expecteds = array(
			'function_result' => true,
			'row_count' => 0,
		);

		$actuals['function_result'] = $this->object->removeUploadedFile($this->goodFile['project_owner'],
			$this->goodFile['project_id'], $this->goodFile['name']);

		$pdoStatement = $this->pdo->query("SELECT COUNT(*) FROM uploaded_files WHERE project_owner = \"{$this->goodFile['project_owner']}\" AND
			project_id = \"{$this->goodFile['project_id']}\" AND name = \"{$this->goodFile['name']}\"");
		$actuals['row_count'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @coverse PDODatabase::removeUploadedFile
	 */
	public function testRemoveUploadedFile_fileDoesNotExist() {
		$actuals = array();
		$expecteds = array(
			'function_result' => true,
			'row_count' => 0,
		);

		$actuals['function_result'] = $this->object->removeUploadedFile($this->newFile['project_owner'],
			$this->newFile['project_id'], $this->newFile['name']);

		$pdoStatement = $this->pdo->query("SELECT COUNT(*) FROM uploaded_files WHERE project_owner = \"{$this->newFile['project_owner']}\" AND
			project_id = \"{$this->newFile['project_id']}\" AND name = \"{$this->newFile['name']}\"");
		$actuals['row_count'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @coverse PDODatabase::changeFileName
	 */
	public function testChangeFileName_fileExists() { 
		$newFileName = "NewFile.txt";
		$actuals = array();
		$expecteds = array(
			"function_return" => true,
			"new_name_row_count" => 1,
			"old_name_row_count" => 0,
		);

		$actuals['function_return'] = $this->object->changeFileName($this->goodFile['project_owner'],
			$this->goodFile['project_id'], $this->goodFile['name'], $newFileName);

		$pdoStatement = $this->pdo->query("SELECT COUNT(*) FROM uploaded_files WHERE project_owner = \"{$this->goodFile['project_owner']}\" AND
			project_id = \"{$this->goodFile['project_id']}\" AND name = \"{$newFileName}\"");
		$actuals['new_name_row_count'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$pdoStatement = $this->pdo->query("SELECT COUNT(*) FROM uploaded_files WHERE project_owner = \"{$this->goodFile['project_owner']}\" AND
			project_id = \"{$this->goodFile['project_id']}\" AND name = \"{$this->goodFile['name']}\"");
		$actuals['old_name_row_count'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();

		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @coverse PDODatabase::changeFileName
	 */
	public function testChangeFileName_fileDoesNotExists() { 
		$newFileName = "NewFile.txt";
		$actuals = array();
		$expecteds = array(
			"function_return" => true, // TODO implementation should be false
		);

		$actuals['function_return'] = $this->object->changeFileName($this->newFile['project_owner'],
			$this->newFile['project_id'], $this->newFile['name'], $newFileName);

		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @coverse PDODatabase::createRun
	 */
	public function testCreateRun_badProject() {

		$actual = $this->object->createRun($this->newProj['owner'], $this->newProj['id'], $this->runCommand, $this->runString);

		$this->assertFalse($actual);
	}
	/**
	 * @coverse PDODatabase::createRun
	 */
	public function testCreateRun_firstRun() {
		$actuals = array();
		$expectedRunId = 3;
		$expecteds = array(
			'function_return' => $expectedRunId,
			'database_row' => array (
				"id" => $expectedRunId,
				"project_owner" => $this->goodProj['owner'],
				"project_id" => $this->goodProj['id'],
				"script_name" => $this->runCommand,
				"script_string" => $this->runString,
				"output" => $this->runOutput,
				"version" => $this->runVersion,
				"run_status" => $this->runDefaultStatus,
				"deleted" => $this->runDefaultDeleted,
			),
		);

		$actuals['function_return'] = $this->object->createRun($this->goodProj['owner'], $this->goodProj['id'], $this->runCommand, $this->runString);

		$pdoStatement = $this->pdo->query("SELECT * FROM script_runs WHERE id = {$actuals['function_return']}");
		$actuals['database_row'] = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @coverse PDODatabase::giveRunPid
	 */
	public function testGiveRunPid_runDoesNotExists() {
		$imaginaryRunId = 99;
		$pid = 78987;

		$actual = $this->object->giveRunPid($imaginaryRunId, $pid);

		$this->assertTrue($actual); // TODO implement should return false
	}
	/**
	 * @coverse PDODatabase::giveRunPid
	 */
	public function testGiveRunPid_runExists() {
		$pid = $this->runningRun['run_status'];
		$runId = $this->completeRun['id'];
		$actuals = array();
		$expecteds = array(
			'function_return' => true,
			'new_pid' => $pid,
		);

		$actuals['function_return'] = $this->object->giveRunPid($runId, $pid);

		$pdoStatement = $this->pdo->query("SELECT run_status FROM script_runs WHERE id = {$runId}");
		$actuals['new_pid'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @coverse PDODatabase::renderCommandRunComplete
	 */
	public function testRenderCommandRunComplete_runComplete() {
		$actuals = array();
		$expecteds = array(
			'system_return_code' => 0,
			'database_status' => -1,
		);
		$runId = $this->completeRun['id'];

		$command = $this->object->renderCommandRunComplete($runId);

		$systemResult = 0;
		system($command, $systemResult);
		$actuals['system_return_code'] = $systemResult;
		$pdoStatement = $this->pdo->query("SELECT run_status FROM script_runs WHERE id = \"{$runId}\"");
		$actuals['database_status'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @coverse PDODatabase::renderCommandRunComplete
	 */
	public function testRenderCommandRunComplete_runRunning() {
		$actuals = array();
		$expecteds = array(
			'system_return_code' => 0,
			'database_status' => -1,
		);
		$runId = $this->runningRun['id'];

		$command = $this->object->renderCommandRunComplete($runId);

		$systemResult = 0;
		system($command, $systemResult);
		$actuals['system_return_code'] = $systemResult;
		$pdoStatement = $this->pdo->query("SELECT run_status FROM script_runs WHERE id = \"{$runId}\"");
		$actuals['database_status'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @coverse PDODatabase::getAllRuns
	 */
	public function testGetAllRuns_projHasRuns() {
		$expected = array($this->completeRun, $this->runningRun);
		foreach($expected as &$run) {
			$run['output'] = $this->runOutput;
			$run['version'] = $this->runVersion;
			$run['deleted'] = $this->runDefaultDeleted;
		}

		$actual = $this->object->getAllRuns($this->goodProj['owner'], $this->goodProj['id']);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @coverse PDODatabase::getAllRuns
	 */
	public function testGetAllRuns_projDoesNotHaveRuns() {
		$expected = array();

		$actual = $this->object->getAllRuns($this->emptyProj['owner'], $this->emptyProj['id']);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @coverse PDODatabase::getAllRuns
	 */
	public function testGetAllRuns_projDoesNotExist() {
		$expected = array();
		
		$actual = $this->object->getAllRuns($this->newProj['owner'], $this->newProj['id']);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @coverse PDODatabase::renderCommandUploadSuccess
	 * @expectedException \Exception
	 */
	public function testRenderCommandUploadSuccess_projectDoesNotExist() {

		$command = $this->object->renderCommandUploadSuccess($this->newProj['owner'],
			$this->newProj['id'], $this->goodFile['name'], $this->goodFile['approx_size']);

		$this->fail();
	}
	/**
	 * @coverse PDODatabase::renderCommandUploadSuccess
	 * @expectedException \Exception
	 */
	public function testRenderCommandUploadSuccess_fileDoesNotExist() {
		$command = $this->object->renderCommandUploadSuccess($this->newFile['project_owner'],
			$this->newFile['project_id'], $this->newFile['name'], $this->newFile['approx_size']);

		$this->fail();
	}
	/**
	 * @coverse PDODatabase::renderCommandUploadSuccess
	 */
	public function testRenderCommandUploadSuccess_fileExists() {
		$actuals = array();
		$newSize = 800;
		$expecteds = array(
			"escapeshellcmd_does_nothing" => false, // TODO implementation should be true?
			"system_return_code" => "0",
			"new_status" => 0,
			"new_size" => $newSize,
		);

		$command = $this->object->renderCommandUploadSuccess($this->downloadingFile['project_owner'],
			$this->downloadingFile['project_id'], $this->downloadingFile['name'], $newSize);

		$actuals['escapeshellcmd_does_nothing'] = ($command == escapeshellcmd($command));
		$systemReturn = 0;
		system($command, $systemRetrun);
		$actuals['system_return_code'] = $systemReturn;
		$pdoStatement = $this->pdo->query("SELECT status FROM uploaded_files WHERE project_owner = \"{$this->downloadingFile['project_owner']}\" AND
			project_id = \"{$this->downloadingFile['project_id']}\" AND name = \"{$this->downloadingFile['name']}\"");
		$actuals['new_status'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$pdoStatement = $this->pdo->query("SELECT approx_size FROM uploaded_files WHERE project_owner = \"{$this->downloadingFile['project_owner']}\" AND
			project_id = \"{$this->downloadingFile['project_id']}\" AND name = \"{$this->downloadingFile['name']}\"");
		$actuals['new_size'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @coverse PDODatabase::renderCommandUploadFailure
	 * @expectedException \Exception
	 */
	public function testRenderCommandUploadFailure_projectDoesNotExist() {

		$command = $this->object->renderCommandUploadFailure($this->newProj['owner'],
			$this->newProj['id'], $this->goodFile['name'], $this->goodFile['approx_size']);

		$this->fail();
	}
	/**
	 * @coverse PDODatabase::renderCommandUploadFailure
	 * @expectedException \Exception
	 */
	public function testRenderCommandUploadFailure_fileDoesNotExist() {
		$command = $this->object->renderCommandUploadFailure($this->newFile['project_owner'],
			$this->newFile['project_id'], $this->newFile['name'], $this->newFile['approx_size']);

		$this->fail();
	}
	/**
	 * @coverse PDODatabase::renderCommandUploadFailure
	 */
	public function testRenderCommandUploadFailure_fileExists() {
		$actuals = array();
		$newSize = 800;
		$expecteds = array(
			"escapeshellcmd_does_nothing" => false, // TODO implementation should be true?
			"system_return_code" => "0",
			"new_status" => 2,
			"new_size" => $newSize,
		);

		$command = $this->object->renderCommandUploadFailure($this->downloadingFile['project_owner'],
			$this->downloadingFile['project_id'], $this->downloadingFile['name'], $newSize);

		$actuals['escapeshellcmd_does_nothing'] = ($command == escapeshellcmd($command));
		error_log($command);
		$systemReturn = 0;
		system($command, $systemRetrun);
		$actuals['system_return_code'] = $systemReturn;
		$pdoStatement = $this->pdo->query("SELECT status FROM uploaded_files WHERE project_owner = \"{$this->downloadingFile['project_owner']}\" AND
			project_id = \"{$this->downloadingFile['project_id']}\" AND name = \"{$this->downloadingFile['name']}\"");
		$actuals['new_status'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$pdoStatement = $this->pdo->query("SELECT approx_size FROM uploaded_files WHERE project_owner = \"{$this->downloadingFile['project_owner']}\" AND
			project_id = \"{$this->downloadingFile['project_id']}\" AND name = \"{$this->downloadingFile['name']}\"");
		$actuals['new_size'] = $pdoStatement->fetchColumn(0);
		$pdoStatement->closeCursor();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @coverse PDODatabase::uploadExists
	 */
	public function testUploadExists_uploadDoesExist() {
		
		$actual = $this->object->uploadExists($this->goodFile['project_owner'],
			$this->goodFile['project_id'], $this->goodFile['name']);

		$this->assertTrue($actual);
	}
	/**
	 * @coverse PDODatabase::uploadExists
	 */
	public function testUploadExists_uploadDoesNotExist() {
		
		$actual = $this->object->uploadExists($this->newFile['project_owner'],
			$this->newFile['project_id'], $this->newFile['name']);

		$this->assertFalse($actual);
	}
}
