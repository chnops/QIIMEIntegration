<?php

namespace Controllers;

class IndexControllerTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("IndexControllerTest");
	}

	private $mockWorkflow = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$mockBuilder = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor();
		$this->mockWorkflow = $mockBuilder->getMock();
	}

	public function setUp() {
		$_SESSION = array();
		$_REQUEST = array();
		$this->object = new IndexController($this->mockWorkflow);
	}

	/**
	 * @covers \Controllers\IndexController::getSubController
	 */
	public function testGetSubController() {
		$actual = $this->object->getSubController();

		$this->assertNull($actual);
	}
	/**
	 * @covers \Controllers\IndexController::setSubController
	 */
	public function testSetSubController() {
		$mockController = $this->getMockBuilder('\Controllers\Controller')->disableOriginalConstructor()->getMockForAbstractClass();

		$this->object->setSubController($mockController);

		$actual = $this->object->getSubController();
		$this->assertEquals($mockController, $actual);
	}
	/**
	 * @covers \Controllers\IndexController::parseSession
	 */
	public function testParseSession_sessionDoesNothing() {
		$_SESSION['username'] = "username";
		$_SESSION['project_id'] = "1";

		$actual = $this->object->parseSession();

		$this->assertNull($actual);
	}
	/**
	 * @covers \Controllers\IndexController::parseInput
	 */
	public function testParseInput_stepIsNotSet() {
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getController"))
			->getMock();
		$expected = new LoginController($mockWorkflow);
		$mockWorkflow->expects($this->never())->method("getController");
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
		$this->object = new IndexController($mockWorkflow);
		$_REQUEST['step'] = true;

		$this->object->parseInput();

		$actual = $this->object->getSubController();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\IndexController::retrievePastResults
	 */
	public function testRetrievePastResults() {

		$actual = $this->object->retrievePastResults();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderInstructions
	 */
	public function testRenderInstructions() {

		$actual = $this->object->renderInstructions();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderForm
	 */
	public function testRenderForm() {

		$actual = $this->object->renderForm();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderHelp
	 */
	public function testRenderHelp() {

		$actual = $this->object->renderHelp();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\IndexController::getSubTitle
	 */
	public function testGetSubTitle() {

		$actual = $this->object->getSubTitle();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderSpecificStyle
	 */
	public function testRenderSpecificStyle() {

		$actual = $this->object->renderSpecificStyle();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\IndexController::renderSpecificScript
	 */
	public function testRenderSpecificScript() {

		$actual = $this->object->renderSpecificScript();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\IndexController::getScriptLibraries
	 */
	public function testGetScriptLibraries() {

		$actual = $this->object->getScriptLibraries();

		$this->assertEmpty($actual);
	}
	
	/**
	 * @covers \Controllers\IndexController::renderOutput
	 */
	public function testRenderOutput_subNotSet() {

		$this->object->renderOutput();

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
