<?php

namespace Models;

class QIIMEWorkflowTest extends \PHPUnit_Framework_TestCase {

	private $operatingSystem;
	private $mockDatabase;
	private $mockWorkflow;

	public static function setUpBeforeClass() {
		error_log("QIIMEWorkflowTest");
	}

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockDatabase = $this->getMockBuilder('\Database\PDODatabase');
		$this->mockDatabase->disableOriginalConstructor();
		$this->mockDatabase = $this->mockDatabase->getMock();
		$this->mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem');
		$this->mockOperatingSystem->disableOriginalConstructor();
		$this->mockOperatingSystem = $this->mockOperatingSystem->getMock();
	}

	public function setUp() {
		$this->workflow = new QIIMEWorkflow($this->mockDatabase, $this->mockOperatingSystem);
	}

	/**
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
	 * @covers QIIMEWorkflow::getNewProject
	 */
	public function testGetNewProject() {
		$expected= new QIIMEProject($this->mockDatabase, $this->mockOperatingSystem);

		$actual = $this->workflow->getNewProject();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers QIIMEWorkflow::findProject
	 */
	public function testFindProject_projectDoesNotExist() {
		$mockBuilder = $this->getMocKBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getProjectName"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("getProjectName")->will($this->returnValue(false));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->findProject("username", "projectId");

		$this->assertNull($actual);
	}
	/**
	 * @covers QIIMEWorkflow::findProject
	 */
	public function testFindProject_projectDoesExist() {
		$expectedUsername = "username";
		$expectedProjectId= "projectId";
		$expectedProjectName= "projectName";
		$mockBuilder = $this->getMocKBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getProjectName"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("getProjectName")->will($this->returnValue($expectedProjectName));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);
		$expected = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$expected->setOwner($expectedUsername);
		$expected->setId($expectedProjectId);
		$expected->setName($expectedProjectName);

		$actual = $this->object->findProject("username", "projectId");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers QIIMEWorkflow::getAllProjects
	 */
	public function testGetAllProjects_userDoesNotExist() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("getAllProjects")->will($this->returnValue(array()));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->getAllProjects("username");

		$this->assertEmpty($actual);
	}
	/**
	 * @covers QIIMEWorkflow::getAllProjects
	 */
	public function testGetAllProjects_userExistsButNoProjects() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("getAllProjects")->will($this->returnValue(array()));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->getAllProjects("username");

		$this->assertEmpty($actual);
	}
	/**
	 * @covers QIIMEWorkflow::getAllProjects
	 */
	public function testGetAllProjects_userExistsAndHasProjects() {
		$expectedUsername = "username";
		$expectedProjectId= "projectId";
		$expectedProjectName= "projectName";
		$projects = array(
			array("name" => $expectedProjectName, "owner" => $expectedUsername, "id" => $expectedProjectId)
		);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("getAllProjects")->will($this->returnValue($projects));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);
		$expectedProject = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$expectedProject->setName($expectedProjectName);
		$expectedProject->setOwner($expectedUsername);
		$expectedProject->setId($expectedProjectId);
		$expected = array($expectedProject);

		$actual = $this->object->getAllProjects("username");

		$this->assertEquals($expected, $actual);
	}
}
