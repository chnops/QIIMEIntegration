<?php

namespace Database;

class PDODatabaseTest extends \PHPUnit_Framework_TestCase {

	private static $testDSN = "sqlite:./data/database.sqlite";

	private $pdo;
	private $database;

	public static function setUpBeforeClass() {
		PDODatabase::overwriteDSN(PDODatabaseTest::$testDSN);
	}
	public function setUp() {
		$this->pdo = new \PDO(PDODatabaseTest::$testDSN);
		$this->database = new PDODatabase();
		$this->pdo->exec("DELETE FROM users");
		$this->pdo->exec("DELETE FROM projects");
		$this->pdo->exec("INSERT INTO users (username, root) VALUES (\"sharpa\", 1)");
		$this->pdo->exec("INSERT INTO projects (id, owner, name) VALUES (1, \"sharpa\", \"Proj1\")");
		$this->pdo->exec("INSERT INTO projects (id, owner, name) VALUES (2, \"sharpa\", \"Proj2\")");
	}

	/**
	 * @test
	 * @covers PDODatabase::userExists
	 */
	public function testUserExists() {
		$nonExistentUser = "asdfasdfasdf";
		$this->assertFalse($this->database->userExists($nonExistentUser));

		$existentUser = "sharpa";
		$this->assertTrue($this->database->userExists($existentUser));
	}

	/**
	 * @test
	 * @covers PDODatabase::createUser
	 */
	public function testCreateUser() {
		$expectedRoot = 2;
		$newUser = "asdfasdf";
		$newRoot = $this->database->createUser($newUser);
		$this->assertSame($expectedRoot, $newRoot);
		$result = $this->pdo->query("SELECT * FROM users WHERE username = \"{$newUser}\"");
		$result = $result->fetch(\PDO::FETCH_ASSOC);
		$this->assertEquals(array("username" => "asdfasdf", "root" => $expectedRoot), $result);

		$this->assertFalse($this->database->createUser($newUser));
		$result = $this->pdo->query("SELECT * FROM users WHERE username = \"{$newUser}\"");
		$result = $result->fetch(\PDO::FETCH_ASSOC);
		$this->assertEquals(array("username" => "asdfasdf", "root" => $expectedRoot), $result);
	}

	/**
	 * @test
	 * @covers PDODatabase::getUserRoot
	 */
	public function testGetUserRoot() {
		$username = "sharpa";
		$this->assertEquals(1, $this->database->getUserRoot($username));
		
		$this->pdo->exec("INSERT INTO users (username, root) VALUES (\"asdfasdf\", 99)");

		$username = "asdfasdf";
		$this->assertEquals(99, $this->database->getUserRoot($username));
	}

	/**
	 * @test
	 * @covers PDODatabase::getAllProjects
	 */
	public function testGetAllProjects() {
		$badUsername = "asdfasdf";
		$this->assertEmpty($this->database->getAllProjects($badUsername));

		$goodUsername = "sharpa";
		$expectedProjects = array(
			array("id" => 1, "owner" => $goodUsername, "name" => "Proj1"),
			array("id" => 2, "owner" => $goodUsername, "name" => "Proj2"),
		);
		$this->assertEquals($expectedProjects, $this->database->getAllProjects($goodUsername));
	}

	/**
	 * @test
	 * @covers PDODatabase::createProject
	 */
	public function testCreateProject() {
		$badUsername = "asdfasdf";
		$projectName = "Proj3";
		$this->assertFalse($this->database->createProject($badUsername, $projectName));

		$expectedId = 3;
		$goodUsername = "sharpa";
		$newId = $this->database->createProject($goodUsername, $projectName);
		$this->assertSame($expectedId, $newId);
		$result = $this->pdo->query("SELECT * FROM projects WHERE owner = \"{$goodUsername}\" AND name = \"{$projectName}\"");
		$result = $result->fetch(\PDO::FETCH_ASSOC);
		$expectedResult = array("id" => 3, "owner" => $goodUsername, "name" => $projectName);
		$this->assertEquals($expectedResult, $result);
		$this->assertFalse($this->database->createProject($goodUsername, $projectName));
	}

	/**
	 * @test
	 * @covers PDODatabase::getProjectName
	 */
	public function testGetProjectName() {
		$badUsername = "asdfasdf";
		$this->assertEquals("ERROR", $this->database->getProjectName($badUsername, 1));
		
		$goodUsername = "sharpa";
		$this->assertEquals("Proj1", $this->database->getProjectName($goodUsername, 1));
		$this->assertEquals("Proj2", $this->database->getProjectName($goodUsername, 2));
	}
}
