<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Controllers;

class IndexControllerTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("IndexControllerTest");
	}
	public static function tearDownAfterClass() {
		\Utils\Helper::setDefaultHelper(NULL);
	}

	private $mockWorkflow = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->getMock();
	}

	public function setUp() {
		$_SESSION = array();
		$_REQUEST = array();
		\Utils\Helper::setDefaultHelper(NULL);
		$this->object = new IndexController($this->mockWorkflow);
	}

	/**
	 * @covers \Controllers\IndexController::getSubController
	 */
	public function testGetSubController() {
		$expected = NULL;
		
		$actual = $this->object->getSubController();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::setSubController
	 */
	public function testSetSubController() {
		$expected = $this->getMockBuilder('\Controllers\Controller')->disableOriginalConstructor()->getMockForAbstractClass();

		$this->object->setSubController($expected);

		$actual = $this->object->getSubController();
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::parseSession
	 */
	public function testParseSession_sessionDoesNothing() {
		$expected = NULL;
		$_SESSION['username'] = "username";
		$_SESSION['project_id'] = "1";

		$actual = $this->object->parseSession();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::parseInput
	 */
	public function testParseInput_stepIsNotSet() {
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getController"))
			->getMock();
		$mockWorkflow->expects($this->never())->method("getController");
		$expected = new LoginController($mockWorkflow);
		$this->object = new IndexController($mockWorkflow);

		$this->object->parseInput();

		$actual = $this->object->getSubController();
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::parseInput
	 */
	public function testParseInput_stepIsSet() {
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getController"))
			->getMock();
		$expected = new SelectProjectController($mockWorkflow);
		$mockWorkflow->expects($this->once())->method("getController")->will($this->returnValue($expected));
		$_REQUEST['step'] = true;
		$this->object = new IndexController($mockWorkflow);

		$this->object->parseInput();

		$actual = $this->object->getSubController();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\IndexController::retrievePastResults
	 */
	public function testRetrievePastResults() {
		$expected = "";

		$actual = $this->object->retrievePastResults();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderInstructions
	 */
	public function testRenderInstructions() {
		$expected = "";

		$actual = $this->object->renderInstructions();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderForm
	 */
	public function testRenderForm() {
		$expected = "";

		$actual = $this->object->renderForm();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderHelp
	 */
	public function testRenderHelp() {
		$expected = "";

		$actual = $this->object->renderHelp();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::getSubTitle
	 */
	public function testGetSubTitle() {
		$expected = "";

		$actual = $this->object->getSubTitle();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderSpecificStyle
	 */
	public function testRenderSpecificStyle() {
		$expected = "";

		$actual = $this->object->renderSpecificStyle();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderSpecificScript
	 */
	public function testRenderSpecificScript() {
		$expected = "";

		$actual = $this->object->renderSpecificScript();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::getScriptLibraries
	 */
	public function testGetScriptLibraries() {
		$expected = array();

		$actual = $this->object->getScriptLibraries();

		$this->assertSame($expected, $actual);
	}
	
	/**
	 * @covers \Controllers\IndexController::renderOutput
	 */
	public function testRenderOutput_subNotSet() {
		$expected = NULL;

		$actual = $this->object->renderOutput();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderOutput
	 */
	public function testRenderOutput_subControllerSet() {
		$mockController = $this->getMockBuilder('\Controllers\Controller')->disableOriginalConstructor()
			->setMethods(array("run"))->getMockForAbstractClass();
		$mockController->expects($this->once())->method("run");
		$this->object->setSubController($mockController);

		$this->object->renderOutput();

	}
}
