<?php

namespace Models;

class QIIMEWorkflowTest extends \PHPUnit_Framework_TestCase {

	private $operatingSystem;
	private $database;
	private $workflow;

	public static function setUpBeforeClass() {
		error_log("QIIMEWorkflowTest");
	}

	public function setUp() {
		$this->operatingSystem = new MacOperatingSystem();
		$this->database = new \Database\PDODatabase($this->operatingSystem);
		$this->workflow = new QIIMEWorkflow($this->database, $this->operatingSystem);
	}

	/**
	 * @test
	 * @covers QIIMEWorkflow::getSteps
	 */
	public function testGetSteps() {
		$expectedSteps = array (
			"login" => "Login",	
			"select" => "Select/create project",
			"upload" => "Upload files",
			"run" => "Run scripts",
			"view" => "View results");
		$this->assertEquals($expectedSteps, $this->workflow->getSteps());
	}

	/**
	 * @test
	 * @covers QIIMEWorkflow::getCurrentStep
	 */
	public function testGetCurrentStep() {
		$controllersInOrder = array (
				new \Controllers\IndexController($this->workflow), 
				new \Controllers\LoginController($this->workflow), 
				new \Controllers\SelectProjectController($this->workflow), 
				new \Controllers\UploadController($this->workflow), 
				new \Controllers\RunScriptsController($this->workflow),
				new \Controllers\ViewResultsController($this->workflow), 
			);
		$stepsInOrder = array ("login","login","select",
			"upload","run","view",);
		$stepsCount = count($stepsInOrder);
		for ($i = 0; $i < $stepsCount; $i++) {
			$this->assertEquals($stepsInOrder[$i],
			   	$this->workflow->getCurrentStep($controllersInOrder[$i]));
		}
	}
	/**
	 * @test
	 * @covers QIIMEWorkflow::getController
	 */
	public function testGetController() {
		$stepsInOrder = array ("index","login","select",
			"upload","run","view",);
		$controllerNamesInOrder = array (
				"Controllers\\LoginController", 
				"Controllers\\LoginController", 
				"Controllers\\SelectProjectController", 
				"Controllers\\UploadController", 
				"Controllers\\RunScriptsController",
				"Controllers\\ViewResultsController", 
			);
		$stepsCount = count($stepsInOrder);
		for ($i = 0; $i < $stepsCount; $i++) {
			$controller = $this->workflow->getController($stepsInOrder[$i]);
			$this->assertEquals($controllerNamesInOrder[$i],
				get_class($controller));
		}
	}

	/**
	 * @test
	 * @covers QIIMEWorkflow::getNewProject
	 */
	public function testGetNewProject() {
		$expectedProject = new QIIMEProject($this->database, $this->operatingSystem);
		$newProject = $this->workflow->getNewProject();
		$this->assertEquals($expectedProject, $newProject);
	}
	/**
	 * @test
	 * @covers QIIMEWorkflow::findProject
	 */
	public function testFindProject() {
		$badUsername = "asdfasdf";
		$goodUsername = "sharpa";
		$goodProjectId1 = 1;
		$goodProjectId2 = 2;

		$this->assertNull($this->workflow->findProject($badUsername, $goodProjectId1));
		$expectedProject1 = $this->workflow->getNewProject();
		$expectedProject1->setName("Proj1");
		$expectedProject1->setOwner($goodUsername);
		$expectedProject1->setId($goodProjectId1);
		$this->assertEquals($expectedProject1, $this->workflow->findProject($goodUsername, $goodProjectId1));
		$expectedProject2 = $this->workflow->getNewProject();
		$expectedProject2->setName("Proj2");
		$expectedProject2->setOwner($goodUsername);
		$expectedProject2->setId($goodProjectId2);
		$this->assertEquals($expectedProject2, $this->workflow->findProject($goodUsername, $goodProjectId2));
	}
	/**
	 * @test
	 * @covers QIIMEWorkflow::getAllProjects
	 */
	public function testGetAllProjects() {
		$badUsername = "asdfasdf";
		$goodUsername = "sharpa";

		$this->assertEmpty($this->workflow->getAllProjects($badUsername));
		
		$expectedProject1 = $this->workflow->getNewProject();
		$expectedProject1->setOwner($goodUsername);
		$expectedProject1->setId(1);
		$expectedProject1->setName("Proj1");
		$expectedProject2 = $this->workflow->getNewProject();
		$expectedProject2->setOwner($goodUsername);
		$expectedProject2->setId(2);
		$expectedProject2->setName("Proj2");
		$expectedProjects = array($expectedProject1, $expectedProject2);

		$this->assertEquals($expectedProjects, $this->workflow->getAllProjects($goodUsername));
	}
}
