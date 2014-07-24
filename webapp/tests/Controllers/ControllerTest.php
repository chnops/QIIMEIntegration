<?php

namespace Controllers;

class ControllerTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ControllerTest");
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
		$this->object = $this->objectBuilder->getMockForAbstractClass();
	}

	/**
	 * @covers \Controllers\Controller::run
	 */
	public function testRun() {
		$this->objectBuilder->setMethods(array("parseSession", "parseInput", "renderOutput"));
		$this->object = $this->objectBuilder->getMockForAbstractClass();
		$this->object->expects($this->once())->method("parseSession")->will($this->returnValue(NULL));
		$this->object->expects($this->once())->method("parseInput")->will($this->returnValue(NULL));
		$this->object->expects($this->once())->method("renderOutput")->will($this->returnValue(NULL));

		$this->object->run();
	}

	/**
	 * @covers \Controllers\Controller::isResultError
	 */
	public function testIsResultError() {
		$actual = $this->object->isResultError();

		$this->assertFalse($actual);
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
		$actual = $this->object->getResult();

		$this->assertEmpty($actual);
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
		$expected = "";
		$this->objectBuilder->setMethods(array("retrievePastResults"));
		$this->object = $this->objectBuilder->getMockForAbstractClass();
		$this->object->expects($this->exactly(2))->method("retrievePastResults")->will($this->returnValue($expected));

		$actual = $this->object->renderPastResults();
		$actual = $this->object->renderPastResults();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::renderPastResults
	 */
	public function testRenderPastResults_somePastResults() {
		$expected = "Past Results";
		$this->objectBuilder->setMethods(array("retrievePastResults"));
		$this->object = $this->objectBuilder->getMockForAbstractClass();
		$this->object->expects($this->once())->method("retrievePastResults")->will($this->returnValue($expected));

		$actual = $this->object->renderPastResults();
		$actual = $this->object->renderPastResults();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getWorkflow
	 */
	public function testGetWorkflow() {
	
		$actual = $this->object->getWorkflow();

		$this->assertEquals($this->mockWorkflow, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getExtraHtml
	 */
	public function testGetExtraHtml_badMarker() {
		$marker = "marker";
	
		$actual = $this->object->getExtraHtml($marker);

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\Controller::getExtraHtml
	 */
	public function testGetExtraHtml_goodMarker() {
		$marker = "post_help";
	
		$actual = $this->object->getExtraHtml($marker);

		$this->assertEmpty($actual);
	}

	/**
	 * @covers \Controllers\Controller::getUsername
	 */
	public function testGetUsername() {

		$actual = $this->object->getUsername();

		$this->assertEmpty($actual);
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

		$actual = $this->object->getProject();

		$this->assertEmpty($actual);
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
		$username = "username";
		$expecteds = array(
			"username" => $username,
			"project" => NULL,
		);
		$actuals = array();
		$_SESSION['username'] = $username;
		
		$this->object->parseSession();
		
		$actuals['username'] = $this->object->getUsername();
		$actuals['project'] = $this->object->getProject();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\Controller::parseSession
	 */
	public function testParseSession_username_project() {
		$username = "username";
		$project = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()->getMockForAbstractClass();
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("findProject"));
		$mockWorkflow = $mockBuilder->getMock();
		$mockWorkflow->expects($this->once())->method("findProject")->will($this->returnValue($project));
		$this->object = $this->getMockBuilder('\Controllers\Controller')->setConstructorArgs(array($mockWorkflow))->getMockForAbstractClass();
		$expecteds = array(
			"username" => $username,
			"project" => $project,
		);
		$actuals = array();
		$_SESSION['username'] = $username;
		$_SESSION['project_id'] = true;
		
		$this->object->parseSession();
		
		$actuals['username'] = $this->object->getUsername();
		$actuals['project'] = $this->object->getProject();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\Controller::renderSessionData
	 */
	public function testRenderSessionData_noUsername_noProject() {
		$username = "";
		$this->object->setUsername($username);
		$project = NULL;
		$this->object->setProject($project);
		$expected = "You are not logged in.";

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::renderSessionData
	 */
	public function testRenderSessionData_noUsername_project() {
		$username = "";
		$this->object->setUsername($username);
		$project = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()->setMethods(array("getName"))
			->getMockForAbstractClass();
		$project->expects($this->never())->method("getName");
		$this->object->setProject($project);
		$expected = "You are not logged in.";

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::renderSessionData
	 */
	public function testRenderSessionData_username_noProject() {
		$username = "username";
		$this->object->setUsername($username);
		$project = NULL;
		$this->object->setProject($project);
		$expected = "You are currently logged in as <strong>{$username}</strong>, but <strong>you have not selected a project.</strong>";

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::renderSessionData
	 */
	public function testRenderSessionData_username_project() {
		$username = "username";
		$this->object->setUsername($username);
		$projectName = "projectName";
		$project = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()->setMethods(array("getName"))
			->getMockForAbstractClass();
		$project->expects($this->once())->method("getName")->will($this->returnValue($projectName));
		$this->object->setProject($project);
		$expected = "You are currently logged in as <strong>{$username}</strong>, and you have selected the project <strong>{$projectName}.</strong>";

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controll::renderSessionData
	 */
	public function testRenderSession_htmlEntities() {
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->any())->method("htmlentities")->will($this->returnValue(""));
		$oldHelper = \Utils\Helper::getHelper();
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->objectBuilder->getMockForAbstractClass();
		$username = "username";
		$this->object->setUsername($username);
		$projectName = "projectName";
		$project = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()->setMethods(array("getName"))
			->getMockForAbstractClass();
		$project->expects($this->once())->method("getName")->will($this->returnValue($projectName));
		$this->object->setProject($project);
		$expected = "You are currently logged in as <strong></strong>, and you have selected the project <strong>.</strong>";

		$actual = $this->object->renderSessionData();

		$this->assertEquals($expected, $actual);
		\Utils\Helper::setDefaultHelper($oldHelper);
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
		$this->objectBuilder->setMethods(array("renderSessionData", "getResult", "isResultError", "renderPastResults"));
		$this->object = $this->objectBuilder->getMockForAbstractClass();
		$this->object->expects($this->once())->method("renderSessionData")->will($this->returnValue("sessionData"));
		$this->object->expects($this->once())->method("getSubtitle")->will($this->returnValue("subTitle"));
		$this->object->expects($this->once())->method("getResult")->will($this->returnValue(false));
		$this->object->expects($this->never())->method("isResultError");
		$this->object->expects($this->once())->method("renderInstructions")->will($this->returnValue(false));
		$this->object->expects($this->once())->method("renderPastResults")->will($this->returnValue(false));
		$this->object->expects($this->once())->method("renderForm")->will($this->returnValue(false));
		$expected =  "<div id=\"session_data\">sessionData</div>\n<h2>subTitle</h2>\n";

		$actual = $this->object->getContent();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getContent
	 */
	public function testGetContent_fieldsNotEmpty_resultNotError() {
		$this->objectBuilder->setMethods(array("renderSessionData", "getResult", "isResultError", "renderPastResults"));
		$this->object = $this->objectBuilder->getMockForAbstractClass();
		$this->object->expects($this->once())->method("renderSessionData")->will($this->returnValue("sessionData"));
		$this->object->expects($this->once())->method("getSubtitle")->will($this->returnValue("subTitle"));
		$this->object->expects($this->once())->method("getResult")->will($this->returnValue("result"));
		$this->object->expects($this->once())->method("isResultError");
		$this->object->expects($this->once())->method("renderInstructions")->will($this->returnValue("instructions"));
		$this->object->expects($this->once())->method("renderPastResults")->will($this->returnValue("pastResults"));
		$this->object->expects($this->once())->method("renderForm")->will($this->returnValue("form"));
		$expected =  "<div id=\"session_data\">sessionData</div>\n<h2>subTitle</h2>\n" .
			"<div id=\"result\">result</div><br/>\n" .
			"<div id=\"instructions\"><em>Instructions (<a id=\"instruction_controller\" onclick=\"hideMe($(this).parent().next());\">hide</a>):</em><div>instructions</div></div>\n" .
			"<div id=\"past_results\"><em>Past results (<a onclick=\"hideMe($(this).parent().next())\">hide</a>)</em><div>pastResults</div></div><br/>\n" .
			"<div class=\"form\">form</div>\n";

		$actual = $this->object->getContent();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\Controller::getContent
	 */
	public function testGetContent_fieldsNotEmpty_resultError() {
		$this->objectBuilder->setMethods(array("renderSessionData", "getResult", "isResultError", "renderPastResults"));
		$this->object = $this->objectBuilder->getMockForAbstractClass();
		$this->object->expects($this->once())->method("renderSessionData")->will($this->returnValue("sessionData"));
		$this->object->expects($this->once())->method("getSubtitle")->will($this->returnValue("subTitle"));
		$this->object->expects($this->once())->method("getResult")->will($this->returnValue("result"));
		$this->object->expects($this->once())->method("isResultError")->will($this->returnValue(true));
		$this->object->expects($this->once())->method("renderInstructions")->will($this->returnValue("instructions"));
		$this->object->expects($this->once())->method("renderPastResults")->will($this->returnValue("pastResults"));
		$this->object->expects($this->once())->method("renderForm")->will($this->returnValue("form"));
		$expected =  "<div id=\"session_data\">sessionData</div>\n<h2>subTitle</h2>\n" .
			"<div id=\"result\" class=\"error\">result</div><br/>\n" .
			"<div id=\"instructions\"><em>Instructions (<a id=\"instruction_controller\" onclick=\"hideMe($(this).parent().next());\">hide</a>):</em><div>instructions</div></div>\n" .
			"<div id=\"past_results\"><em>Past results (<a onclick=\"hideMe($(this).parent().next())\">hide</a>)</em><div>pastResults</div></div><br/>\n" .
			"<div class=\"form\">form</div>\n";

		$actual = $this->object->getContent();

		$this->assertEquals($expected, $actual);


	}
}
