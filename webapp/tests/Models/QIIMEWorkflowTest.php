<?php

namespace Models;

class QIIMEWorkflowTest extends \PHPUnit_Framework_TestCase {

	private $operatingSystem;
	private $database;
	private $workflow;

	public function setUp() {
		$this->operatingSystem = new MacOperatingSystem();
		$this->database = new \Database\PDODatabase();
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
			"make_otu" => "Create OTU table",
			"make_phylogeny" => "Perform phylogeny analysis [optional]",
			"view" => "View results");
		$this->assertEquals($expectedSteps, $this->workflow->getSteps());
	}

	/**
	 * @test
	 * @covers QIIMEWorkflow::getNextStep
	 */
	public function testGetNextStep() {
		$stepsInOrder = array ("login", "select", "upload", "make_otu", "make_phylogeny", "view");
		$stepsCount = count($stepsInOrder);
		for ($i = 0; $i < $stepsCount - 1; $i++) {
			$expected = $stepsInOrder[$i + 1];
			$this->assertEquals($expected, $this->workflow->getNextStep($stepsInOrder[$i]));
		}
		$this->assertEquals($stepsInOrder[$stepsCount - 1], $this->workflow->getNextStep($stepsInOrder[$stepsCount - 1]));
		$this->assertEquals($stepsInOrder[0], $this->workflow->getNextStep("notAStep"));
	}

	/**
	 * @test
	 * @covers QIIMEWorkflow::getPreviousStep
	 */
	public function testGetPreviousStep() {
		$stepsInReverseOrder = array ("view", "make_phylogeny", "make_otu", "upload", "select", "login");
		$stepsCount = count($stepsInReverseOrder);
		for ($i = 0; $i < $stepsCount - 1; $i++) {
			$expected = $stepsInReverseOrder[$i + 1];
			$this->assertEquals($expected, $this->workflow->getPreviousStep($stepsInReverseOrder[$i]));
		}
		$this->assertEquals($stepsInReverseOrder[$stepsCount - 1], $this->workflow->getPreviousStep($stepsInReverseOrder[$stepsCount - 1]));
		$this->assertEquals($stepsInReverseOrder[0], $this->workflow->getPreviousStep("notAStep"));
	}

	/**
	 * @test
	 * @covers QIIMEWorkflow::getCurrentStep
	 */
	public function testGetCurrentStep() {
		$controllersInOrder = array (
				new \Controllers\TestController($this->database, $this->workflow), 
				new \Controllers\IndexController($this->database, $this->workflow), 
				new \Controllers\LoginController($this->database, $this->workflow), 
				new \Controllers\SelectProjectController($this->database, $this->workflow), 
				new \Controllers\UploadController($this->database, $this->workflow), 
				new \Controllers\MakeOtuController($this->database, $this->workflow), 
				new \Controllers\MakePhylogenyController($this->database, $this->workflow), 
				new \Controllers\ViewResultsController($this->database, $this->workflow), 
			);
		$stepsInOrder = array ("test","login","login","select",
			"upload","make_otu","make_phylogeny","view",);
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
		$stepsInOrder = array ("test","index","login","select",
			"upload","make_otu","make_phylogeny","view",);
		$controllerNamesInOrder = array (
				"Controllers\\TestController", 
				"Controllers\\LoginController", 
				"Controllers\\LoginController", 
				"Controllers\\SelectProjectController", 
				"Controllers\\UploadController", 
				"Controllers\\MakeOtuController", 
				"Controllers\\MakePhylogenyController", 
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
		$expectedProject = new QIIMEProject($this->database, $this->workflow, $this->operatingSystem);
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
