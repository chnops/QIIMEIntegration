<?php

namespace Controllers;

class ControllerTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ControllerTest");
	}
	public static function tearDownAfterClass() {
		\Utils\Helper::setDefaultHelper(NULL);
	}
	
	private $mockWorkflow = NULL;
	private $objectBuilder = NULL;
	private $object = NULL;
	
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$mockBuilder = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor();
		$this->mockWorkflow = $mockBuilder->getMock();

		$this->objectBuilder = $this->getMockBuilder('\Controllers\Controller')
			->setConstructorArgs(array($this->mockWorkflow));
	}
	
	public function setUp() {
		$_SESSION = array();
		\Utils\Helper::setDefaultHelper(NULL);
		$this->object = $this->objectBuilder->getMockForAbstractClass();
	}

	/**
	 * @covers \Controllers\Controller::run
	 */
	public function testRun() {
		$this->object = $this->objectBuilder
			->setMethods(array("parseSession", "parseInput", "renderOutput"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("parseSession")->will($this->returnValue(NULL));
		$this->object->expects($this->once())->method("parseInput")->will($this->returnValue(NULL));
		$this->object->expects($this->once())->method("renderOutput")->will($this->returnValue(NULL));

		$this->object->run();

	}

	/**
	 * @covers \Controllers\Controller::isResultError
	 */
	public function testIsResultError() {
		$expected = false;

		$actual = $this->object->isResultError();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::setIsResultError
	 */
	public function testSetIsResultError() {
		$expected = true;
		
		$this->object->setIsResultError($expected);

		$actual = $this->object->isResultError();
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getResult
	 */
	public function testGetResult() {
		$expected = "";

		$actual = $this->object->getResult();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::setResult
	 */
	public function testSetResult() {
		$expected = "result";
		
		$this->object->setResult($expected);

		$actual = $this->object->getResult();
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::renderPastResults
	 */
	public function testRenderPastResults_noPastResults() {
		$expecteds = array(
			"first_call" => "",
			"second_call" => "",
		);
		$actuals = array();
		$this->object = $this->objectBuilder
			->setMethods(array("retrievePastResults"))
			->getMockForAbstractClass();
		$this->object->expects($this->exactly(2))->method("retrievePastResults")->will($this->returnValue(""));

		$actuals['first_call'] = $this->object->renderPastResults();
		$actuals['second_call'] = $this->object->renderPastResults();

		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\Controller::renderPastResults
	 */
	public function testRenderPastResults_somePastResults() {
		$expectedResult = "Past Results";
		$expecteds = array(
			"first_call" => $expectedResult,
			"second_call" => $expectedResult,
		);
		$actuals = array();
		$this->object = $this->objectBuilder
			->setMethods(array("retrievePastResults"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("retrievePastResults")->will($this->returnValue($expectedResult));

		$actuals['first_call'] = $this->object->renderPastResults();
		$actuals['second_call'] = $this->object->renderPastResults();

		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\Controller::getWorkflow
	 */
	public function testGetWorkflow() {
		$expected = $this->mockWorkflow;
	
		$actual = $this->object->getWorkflow();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getExtraHtml
	 */
	public function testGetExtraHtml_goodMarker() {
		$expected = "";
		$marker = "post_help";
	
		$actual = $this->object->getExtraHtml($marker);

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Controllers\Controller::getUsername
	 */
	public function testGetUsername() {
		$expected = "";

		$actual = $this->object->getUsername();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::setUsername
	 */
	public function testSetUsername() {
		$expected = "username";

		$this->object->setUsername($expected);

		$actual = $this->object->getUsername();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\Controller::getProject
	 */
	public function testGetProject() {
		$expected = NULL;

		$actual = $this->object->getProject();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::setProject
	 */
	public function testSetProject() {
		$expected = "project";

		$this->object->setProject($expected);

		$actual = $this->object->getProject();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\Controller::getDisabled
	 */
	public function testGetDisabled() {
		$expected = "";
	
		$actual = $this->object->getDisabled();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getDisabled
	 */
	public function testSetDisabled() {
		$expected = " disabled";
	
		$this->object->setDisabled($expected);

		$actual = $this->object->getDisabled();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\Controller::parseSession
	 */
	public function testParseSession_noUsername_noProject() {
		$expecteds = array(
			"username" => "",
			"project" => NULL,
		);
		$actuals = array();

		$this->object->parseSession();
		
		$actuals['username'] = $this->object->getUsername();
		$actuals['project'] = $this->object->getProject();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\Controller::parseSession
	 */
	public function testParseSession_noUsername_project() {
		$expecteds = array(
			"username" => "",
			"project" => NULL,
		);
		$actuals = array();
		$_SESSION['project_id'] = 2;
		
		$this->object->parseSession();
		
		$actuals['username'] = $this->object->getUsername();
		$actuals['project'] = $this->object->getProject();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\Controller::parseSession
	 */
	public function testParseSession_username_noProject() {
		$expectedUsername = "username";
		$expecteds = array(
			"username" => $expectedUsername,
			"project" => NULL,
		);
		$actuals = array();
		$_SESSION['username'] = $expectedUsername;
		
		$this->object->parseSession();
		
		$actuals['username'] = $this->object->getUsername();
		$actuals['project'] = $this->object->getProject();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\Controller::parseSession
	 */
	public function testParseSession_username_project() {
		$expectedUsername = "username";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$expecteds = array(
			"username" => $expectedUsername,
			"project" => $mockProject,
		);
		$actuals = array();
		$actuals = array();
 		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("findProject"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("findProject")->will($this->returnValue($mockProject));
		$_SESSION['username'] = $expectedUsername;
		$_SESSION['project_id'] = true;
		$this->object = $this->getMockBuilder('\Controllers\Controller')->setConstructorArgs(array($mockWorkflow))->getMockForAbstractClass();
		
		$this->object->parseSession();
		
		$actuals['username'] = $this->object->getUsername();
		$actuals['project'] = $this->object->getProject();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\Controller::renderSessionData
	 */
	public function testRenderSessionData_noUsername_noProject() {
		$expected = "You are not logged in.";
		$expectedUsername = "";
		$expectedProject = NULL;
		$this->object->setUsername($expectedUsername);
		$this->object->setProject($expectedProject);

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::renderSessionData
	 */
	public function testRenderSessionData_noUsername_project() {
		$expected = "You are not logged in.";
		$expectedUsername = "";
		$expectedProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()->setMethods(array("getName"))
			->getMockForAbstractClass();
		$expectedProject->expects($this->never())->method("getName");
		$this->object->setusername($expectedUsername);
		$this->object->setProject($expectedProject);

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::renderSessionData
	 */
	public function testRenderSessionData_username_noProject() {
		$expectedUsername = "username";
		$expected = "You are currently logged in as <strong>{$expectedUsername}</strong>, but <strong>you have not selected a project.</strong>";
		$expectedProject = NULL;
		$this->object->setUsername($expectedUsername);
		$this->object->setProject($expectedProject);

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::renderSessionData
	 */
	public function testRenderSessionData_username_project() {
		$expectedUsername = "username";
		$expectedProjectName = "projectName";
		$expected = "You are currently logged in as <strong>{$expectedUsername}</strong>, and you have selected the project <strong>{$expectedProjectName}.</strong>";
		$expectedProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()->setMethods(array("getName"))
			->getMockForAbstractClass();
		$expectedProject->expects($this->once())->method("getName")->will($this->returnValue($expectedProjectName));
		$this->object->setUsername($expectedUsername);
		$this->object->setProject($expectedProject);

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controll::renderSessionData
	 */
	public function testRenderSession_htmlEntities() {
		$expected = "You are currently logged in as <strong></strong>, and you have selected the project <strong>.</strong>";
		$expectedUsername = "username";
		$expectedProjectName = "projectName";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$expectedProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()->setMethods(array("getName"))
			->getMockForAbstractClass();
		$mockHelper->expects($this->any())->method("htmlentities")->will($this->returnValue(""));
		$expectedProject->expects($this->once())->method("getName")->will($this->returnValue($expectedProjectName));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->objectBuilder->getMockForAbstractClass();
		$this->object->setUsername($expectedUsername);
		$this->object->setProject($expectedProject);

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\Controller::renderOutput
	 */
	public function testRenderOutput() {
		$expected = "File: views/template.php\n";
		ob_start();

		$this->object->renderOutput();

		$actual = ob_get_clean();
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getContent
	 */
	public function testGetContent_fieldsEmpty() {
		$expected =  "<div id=\"session_data\">sessionData</div>\n<h2>subTitle</h2>\n";
		$this->object = $this->objectBuilder
			->setMethods(array("renderSessionData", "getResult", "isResultError", "renderPastResults"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("renderSessionData")->will($this->returnValue("sessionData"));
		$this->object->expects($this->once())->method("getSubtitle")->will($this->returnValue("subTitle"));
		$this->object->expects($this->once())->method("getResult")->will($this->returnValue(false));
		$this->object->expects($this->never())->method("isResultError");
		$this->object->expects($this->once())->method("renderInstructions")->will($this->returnValue(false));
		$this->object->expects($this->once())->method("renderPastResults")->will($this->returnValue(false));
		$this->object->expects($this->once())->method("renderForm")->will($this->returnValue(false));

		$actual = $this->object->getContent();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getContent
	 */
	public function testGetContent_fieldsNotEmpty_resultNotError() {
		$expected =  "<div id=\"session_data\">sessionData</div>\n<h2>subTitle</h2>\n" .
			"<div id=\"result\">result</div><br/>\n" .
			"<div id=\"instructions\"><em>Instructions (<a id=\"instruction_controller\" onclick=\"hideMe($(this).parent().next());\">hide</a>):</em><div>instructions</div></div>\n" .
			"<div id=\"past_results\"><em>Past results (<a onclick=\"hideMe($(this).parent().next())\">hide</a>)</em><div>pastResults</div></div><br/>\n" .
			"<div class=\"form\">form</div>\n";
		$this->object = $this->objectBuilder
			->setMethods(array("renderSessionData", "getResult", "isResultError", "renderPastResults"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("renderSessionData")->will($this->returnValue("sessionData"));
		$this->object->expects($this->once())->method("getSubtitle")->will($this->returnValue("subTitle"));
		$this->object->expects($this->once())->method("getResult")->will($this->returnValue("result"));
		$this->object->expects($this->once())->method("isResultError");
		$this->object->expects($this->once())->method("renderInstructions")->will($this->returnValue("instructions"));
		$this->object->expects($this->once())->method("renderPastResults")->will($this->returnValue("pastResults"));
		$this->object->expects($this->once())->method("renderForm")->will($this->returnValue("form"));

		$actual = $this->object->getContent();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getContent
	 */
	public function testGetContent_fieldsNotEmpty_resultError() {
		$expected =  "<div id=\"session_data\">sessionData</div>\n<h2>subTitle</h2>\n" .
			"<div id=\"result\" class=\"error\">result</div><br/>\n" .
			"<div id=\"instructions\"><em>Instructions (<a id=\"instruction_controller\" onclick=\"hideMe($(this).parent().next());\">hide</a>):</em><div>instructions</div></div>\n" .
			"<div id=\"past_results\"><em>Past results (<a onclick=\"hideMe($(this).parent().next())\">hide</a>)</em><div>pastResults</div></div><br/>\n" .
			"<div class=\"form\">form</div>\n";
		$this->object = $this->objectBuilder
			->setMethods(array("renderSessionData", "getResult", "isResultError", "renderPastResults"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("renderSessionData")->will($this->returnValue("sessionData"));
		$this->object->expects($this->once())->method("getSubtitle")->will($this->returnValue("subTitle"));
		$this->object->expects($this->once())->method("getResult")->will($this->returnValue("result"));
		$this->object->expects($this->once())->method("isResultError")->will($this->returnValue(true));
		$this->object->expects($this->once())->method("renderInstructions")->will($this->returnValue("instructions"));
		$this->object->expects($this->once())->method("renderPastResults")->will($this->returnValue("pastResults"));
		$this->object->expects($this->once())->method("renderForm")->will($this->returnValue("form"));

		$actual = $this->object->getContent();

		$this->assertEquals($expected, $actual);
	}
}
