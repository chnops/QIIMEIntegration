<?php

namespace Controllers;

class RunScriptsControllerTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("RunScriptsControllerTest");
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
			->setMethods(array("getStep"))
			->getMock();
		$this->mockWorkflow->expects($this->any())->method("getStep")->will($this->returnValue("run"));
	}
	public function setUp() {
		$_POST = array();
		\Utils\Helper::setDefaultHelper(NULL);
		$this->object = new RunScriptsController($this->mockWorkflow);
	}

	/**
	 * @covers \Controllers\RunScriptsController::getSubTitle
	 */
	public function testGetSubTitle() {
		$expected = "Run Scripts";

		$actual = $this->object->getSubTitle();

		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @covers \Controllers\RunScriptsController::retrievePastResults
	 */
	public function testRetrievePastResults_projectNotSet() {
		$expected = "";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray", "htmlentities"))
			->getMock();
		$mockHelper->expects($this->never())->method("categorizeArray")->will($this->returnArgument(0));
		$mockHelper->expects($this->never())->method("categorizeArray")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new RunScriptsController($this->mockWorkflow);
		$this->object->setProject(NULL);

		$actual = $this->object->retrievePastResults();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::retrievePastResults
	 */
	public function testRetrievePastResults_zeroPastScriptRuns() {
		$expected = "";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getPastScriptRuns"))
			->getMockForAbstractClass();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray", "htmlentities"))
			->getMock();
		$mockProject->expects($this->once())->method("getPastScriptRuns")->will($this->returnValue(array()));
		$mockHelper->expects($this->never())->method("categorizeArray")->will($this->returnArgument(0));
		$mockHelper->expects($this->never())->method("categorizeArray")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new RunScriptsController($this->mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->retrievePastResults();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::retrievePastResults
	 */
	public function testRetrievePastResults_manyPastScriptRuns() {
		$expected = "<div class=\"hideable accordion\" id=\"past_results_unrun\">This script has not been run yet.</div>\n" .
			"<div class=\"hideable accordion\" id=\"past_results_run\"><h4 onclick=\"hideMe($(this).next())\">Run 1 (<em>done</em>)</h4>" .
			"<div><strong>User input:</strong> run.py --no_result_files=T</div>" . 
			"<h4 onclick=\"hideMe($(this).next())\">Run 2 (<em>still running</em>)</h4>" .
			"<div><strong>User input:</strong> run.py --no_result_files=T --run_for_a_long_time=T</div>" . 
			"<h4 onclick=\"hideMe($(this).next())\">Run 3 (<em>done</em>)</h4>" .
			"<div><strong>User input:</strong> run.py<br/><strong>Generated files</strong><ul><li>File1</li><li>File2</li></ul></div></div>\n";
		$expectedScriptRuns = array(
			"run" => array(
				array("is_finished" => true, "id" => 1, "input" => "run.py --no_result_files=T", "file_names" => array()),
				array("is_finished" => false, "id" => 2, "input" => "run.py --no_result_files=T --run_for_a_long_time=T", "file_names" => array()),
				array("is_finished" => true, "id" => 3, "input" => "run.py", "file_names" => array("File1", "File2")),
			),
		);
		$expectedScripts = array("unrun" => NULL, "run" => NULL);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getPastScriptRuns", "getScripts"))
			->getMockForAbstractClass();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray", "htmlentities"))
			->getMock();
		$mockProject->expects($this->once())->method("getPastScriptRuns")->will($this->returnValue($expectedScriptRuns));
		$mockProject->expects($this->once())->method("getScripts")->will($this->returnValue($expectedScripts));
		$mockHelper->expects($this->once())->method("categorizeArray")->will($this->returnArgument(0));
		$mockHelper->expects($this->exactly(3 + 2))->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new RunScriptsController($this->mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->retrievePastResults();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\RunScriptsController::parseInput
	 */
	public function testParseInput_notLoggedOn() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "In order to run scripts, you must be logged in and have a project selected.",
		);
		$actuals = array();
		$_POST['step'] = "run";

		$this->object->parseInput();
		
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();	
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\RunScriptsController::parseInput
	 */
	public function testParseInput_loggedOnButNoProjectSelected() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "In order to run scripts, you must be logged in and have a project selected.",
		);
		$actuals = array();
		$_POST['step'] = "run";
		$this->object->setUsername("username");

		$this->object->parseInput();
		
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();	
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\RunScriptsController::parseInput
	 */
	public function testParseInput_loggedOn_noPOST() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "",
			"script_id" => -1,
		);
		$actuals = array();
		$this->object->setUsername("username");
		$this->object->setProject(true);
		$this->object->setScriptId(-1);

		$this->object->parseInput();
		
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();	
		$actuals['script_id'] = $this->object->getScriptId();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\RunScriptsController::parseInput
	 */
	public function testParseInput_loggedOn_projectThrowsException() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Unable to run script: message",
			"script_id" => 1,
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("runScript"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("runScript")->will($this->throwException(new \Exception("message")));
		$_POST['step'] = "run";
		$_POST['script'] = $expecteds['script_id'];
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);

		$this->object->parseInput();
		
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();	
		$actuals['script_id'] = $this->object->getScriptId();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\RunScriptsController::parseInput
	 */
	public function testParseInput_loggedOn_projectDoesNotThrowException() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "message",
			"script_id" => 1,
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("runScript"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("runScript")->will($this->returnValue("message"));
		$_POST['step'] = "run";
		$_POST['script'] = $expecteds['script_id'];
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);

		$this->object->parseInput();
		
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();	
		$actuals['script_id'] = $this->object->getScriptId();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Controller\RunScriptsController::getScriptId
	 */
	public function testGetScriptId() {
		$expected = "";

		$actual = $this->object->getScriptId();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controller\RunScriptsController::setScriptId
	 */
	public function testSetScriptId() {
		$expected = 1;

		$this->object->setScriptId($expected);

		$actual = $this->object->getScriptId();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\RunScriptsController::renderInstructions
	 */
	public function testRenderInstructions_projectNotSet() {
		$expected = "<p>From here, you can run any of the scripts that make up your workflow.  They are listed below in the order they are likely to be run,
			although it is possible that you will run them in a totally different order (see the help bar on the right for rules and requirements for specific scripts)</p>\noverview";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("renderOverview"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockProject->expects($this->once())->method("renderOverview")->will($this->returnValue("overview"));
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);

		$actual = $this->object->renderInstructions();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::renderInstructions
	 */
	public function testRenderInstructions_projectSet() {
		$expected = "<p>From here, you can run any of the scripts that make up your workflow.  They are listed below in the order they are likely to be run,
			although it is possible that you will run them in a totally different order (see the help bar on the right for rules and requirements for specific scripts)</p>\noverview";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("renderOverview"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockProject->expects($this->once())->method("renderOverview")->will($this->returnValue("overview"));
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->renderInstructions();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\RunScriptsController::renderForm
	 */
	public function testRenderForm_projectNotSet_zeroScripts() {
		$expected = "";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getScripts"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockProject->expects($this->once())->method("getScripts")->will($this->returnValue(array()));
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::renderForm
	 */
	public function testRenderForm_projectNotSet_manyScripts() {
		$expected = "<div class=\"hideable script_form\" id=\"form_script\">1</div>\n" .
			"<div class=\"hideable script_form\" id=\"form_script\">1</div>\n";
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getHtmlId", "renderAsForm"))
			->getMockForAbstractClass();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getScripts"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockScript->expects($this->exactly(2))->method("renderAsForm")->will($this->returnArgument(0));
		$mockScript->expects($this->exactly(2))->method("getHtmlId")->will($this->returnValue("script"));
		$mockProject->expects($this->once())->method("getScripts")->will($this->returnValue(array($mockScript, $mockScript)));
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::renderForm
	 */
	public function testRenderForm_projectSet_zeroScripts() {
		$expected = "";
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getHtmlId", "renderAsForm"))
			->getMockForAbstractClass();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getScripts"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockScript->expects($this->never())->method("renderAsForm")->will($this->returnArgument(0));
		$mockScript->expects($this->never())->method("getHtmlId")->will($this->returnValue("script"));
		$mockProject->expects($this->once())->method("getScripts")->will($this->returnValue(array()));
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::renderForm
	 */
	public function testRenderForm_projectSet_manyScript() {
		$expected = "<div class=\"hideable script_form\" id=\"form_script\"></div>\n" .
			"<div class=\"hideable script_form\" id=\"form_script\"></div>\n";
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getHtmlId", "renderAsForm"))
			->getMockForAbstractClass();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getScripts"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockScript->expects($this->exactly(2))->method("renderAsForm")->will($this->returnArgument(0));
		$mockScript->expects($this->exactly(2))->method("getHtmlId")->will($this->returnValue("script"));
		$mockProject->expects($this->once())->method("getScripts")->will($this->returnValue(array($mockScript, $mockScript)));
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\RunScriptsController::renderHelp
	 */
	public function testRenderHelp_projectNotSet_zeroScripts() {
		$expected = "";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getScripts"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockProject->expects($this->once())->method("getScripts")->will($this->returnValue(array()));
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);

		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::renderHelp
	 */
	public function testRenderHelp_projectNotSet_manyScripts() {
		$expected = "<div class=\"hideable\" id=\"help_script\">\nhelp</div>\n" .
			"<div class=\"hideable\" id=\"help_script\">\nhelp</div>\n";
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getHtmlId", "renderHelp"))
			->getMockForAbstractClass();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getScripts"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockScript->expects($this->exactly(2))->method("getHtmlId")->will($this->returnValue("script"));
		$mockScript->expects($this->exactly(2))->method("renderHelp")->will($this->returnValue("help"));
		$mockProject->expects($this->once())->method("getScripts")->will($this->returnValue(array($mockScript, $mockScript)));
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);

		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::renderHelp
	 */
	public function testRenderHelp_projectSet_zeroScripts() {
		$expected = "";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getScripts"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockProject->expects($this->once())->method("getScripts")->will($this->returnValue(array()));
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::renderHelp
	 */
	public function testRenderHelp_projectSet_manyScripts() {
		$expected = "<div class=\"hideable\" id=\"help_script\">\nhelp</div>\n" .
			"<div class=\"hideable\" id=\"help_script\">\nhelp</div>\n";
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getHtmlId", "renderHelp"))
			->getMockForAbstractClass();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getScripts"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockScript->expects($this->exactly(2))->method("getHtmlId")->will($this->returnValue("script"));
		$mockScript->expects($this->exactly(2))->method("renderHelp")->will($this->returnValue("help"));
		$mockProject->expects($this->once())->method("getScripts")->will($this->returnValue(array($mockScript, $mockScript)));
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new RunScriptsController($mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @covers \Controllers\RunScriptsController::renderSpecificStyle
	 */
	public function testRenderSpecificStyle() {
		$expected = "div.script_form input[type=\"text\"],div.script_form input[type=\"file\"],select{display:block}
			div#project_overview{border-width:1px;padding:.5em;overflow:auto}
			div#project_overview div{margin:1em 0em 1.5em 0em;white-space:nowrap}
			div#project_overview span{margin:0em 1em}
			.accordion h4,.accordion div{margin-bottom:0em;margin-top:0em;padding:.25em;white-space:nowrap}
			select[size]{padding:.5em .5em 1.5em .5em}
			table.either_or{border:1px solid #999966;display:inline-block;padding:.25em}
			table.either_or td{padding:.25em;text-align:center}
			table.either_or tbody tr:first-child td {border-bottom:1px solid #999966}
			table.either_or td:not(:first-child) {border-left:1px solid #999966}
			#per_param_help{white-space:pre-line;overflow:auto;border-top-width:1px}";
		
		$actual = $this->object->renderSpecificStyle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\RunScriptsController::renderSpecificScript
	 */
	public function testRenderSpecificScript_scriptIdNotSet() {
		$expected = "$(function() {hideableFields=['form', 'help', 'past_results'];;$('.param_help').click(function() {
			$('#per_param_help').html('-loading-').load('help/' + $(this).attr('id') + '.txt') });;$('.accordion div').css('display', 'none')});";

		$actual = $this->object->renderSpecificScript();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::renderSpecificScript
	 */
	public function testRenderSpecificScript_scriptIdSet() {
		$expectedId = "scriptId";
		$expected = "$(function() {hideableFields=['form', 'help', 'past_results'];displayHideables('{$expectedId}');;$('.param_help').click(function() {
			$('#per_param_help').html('-loading-').load('help/' + $(this).attr('id') + '.txt') });;$('.accordion div').css('display', 'none')});";
		$this->object->setScriptId($expectedId);

		$actual = $this->object->renderSpecificScript();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\RunScriptsController::getScriptLibraries
	 */
	public function testGetScriptLibraries() {
		$expected = array("parameter_relationships.js");

		$actual = $this->object->getScriptLibraries();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\RunScriptsController::getExtraHtml
	 */
	public function testGetExtraHtml_postHelp() {
		$expected = "<p>Parameter-specific help (<a onclick=\"hideMe($(this).parent().next());\">hide</a>)</p>
				<div id=\"per_param_help\"></div>";

		$actual = $this->object->getExtraHtml("post_help");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\RunScriptsController::getExtraHtml
	 */
	public function testGetExtraHtml_other() {
		$expected = "";

		$actual = $this->object->getExtraHtml("post_help" . "_other");

		$this->assertEquals($expected, $actual);
	}
}
