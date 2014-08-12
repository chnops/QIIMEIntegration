<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models;

class QIIMEWorkflowTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("QIIMEWorkflowTest");
	}

	private $username = "username";
	private $projectId = "projectId";
	private $projectName = "projectName";

	private $mockDatabase = NULL;
	private $mockOperatingSystem = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->getMock();
		$this->mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->getMock();
	}

	public function setUp() {
		$this->object = new QIIMEWorkflow($this->mockDatabase, $this->mockOperatingSystem);
	}

	/**
	 * @covers QIIMEWorkflow::getSteps
	 */
	public function testGetSteps() {
		$expected = array (
			"login" => "Login",	
			"select" => "Select/create project",
			"upload" => "Upload files",
			"run" => "Run scripts",
			"view" => "View results");

		$actual = $this->object->getSteps();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers QIIMEWorkflow::getCurrentStep
	 */
	public function testGetCurrentStep() {
		$expected = array ("login","login","select",
			"upload","run","view",);
		$actual = array();
		$controllersInOrder = array (
			new \Controllers\IndexController($this->object), 
			new \Controllers\LoginController($this->object), 
			new \Controllers\SelectProjectController($this->object), 
			new \Controllers\UploadController($this->object), 
			new \Controllers\RunScriptsController($this->object),
			new \Controllers\ViewResultsController($this->object), 
		);
		foreach ($controllersInOrder as $controller) {

			$actual[] = $this->object->getCurrentStep($controller);

		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers QIIMEWorkflow::getController
	 */
	public function testGetController() {
		$expected = array (
			new \Controllers\LoginController($this->object), 
			new \Controllers\LoginController($this->object), 
			new \Controllers\SelectProjectController($this->object), 
			new \Controllers\UploadController($this->object), 
			new \Controllers\RunScriptsController($this->object),
			new \Controllers\ViewResultsController($this->object), 
		);
		$actual = array();
		$stepsInOrder = array ("index","login","select",
			"upload","run","view",);

		foreach ($stepsInOrder as $step) {

			$actual[] = $this->object->getController($step);

		}
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers QIIMEWorkflow::getNewProject
	 */
	public function testGetNewProject() {
		$expected= new QIIMEProject($this->mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->getNewProject();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers QIIMEWorkflow::findProject
	 */
	public function testFindProject_projectDoesNotExist() {
		$expected = NULL;
		$mockDatabase = $this->getMocKBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getProjectName"))
			->getMock();
		$mockDatabase->expects($this->once())->method("getProjectName")->will($this->returnValue(false));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->findProject("username", "projectId");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers QIIMEWorkflow::findProject
	 */
	public function testFindProject_projectDoesExist() {
		$mockDatabase = $this->getMocKBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getProjectName"))
			->getMock();
		$expected = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$expected->setOwner($this->username);
		$expected->setId($this->projectId);
		$expected->setName($this->projectName);
		$mockDatabase->expects($this->once())->method("getProjectName")->will($this->returnValue($this->projectName));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->findProject("username", "projectId");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers QIIMEWorkflow::getAllProjects
	 */
	public function testGetAllProjects_userDoesNotExist() {
		$expected = array();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"))
			->getMock();
		$mockDatabase->expects($this->once())->method("getAllProjects")->will($this->returnValue(array()));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->getAllProjects("username");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers QIIMEWorkflow::getAllProjects
	 */
	public function testGetAllProjects_userExistsButNoProjects() {
		$expected = array();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"))
			->getMock();
		$mockDatabase->expects($this->once())->method("getAllProjects")->will($this->returnValue(array()));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->getAllProjects("username");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers QIIMEWorkflow::getAllProjects
	 */
	public function testGetAllProjects_userExistsAndHasProjects() {
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("getAllProjects"))
			->getMock();
		$expectedProject = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$expectedProject->setName($this->projectName);
		$expectedProject->setOwner($this->username);
		$expectedProject->setId($this->projectId);
		$expected = array($expectedProject);
		$projects = array(
			array("name" => $this->projectName, "owner" => $this->username, "id" => $this->projectId)
		);
		$mockDatabase->expects($this->once())->method("getAllProjects")->will($this->returnValue($projects));
		$this->object = new QIIMEWorkflow($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->getAllProjects("username");

		$this->assertEquals($expected, $actual);
	}
}
