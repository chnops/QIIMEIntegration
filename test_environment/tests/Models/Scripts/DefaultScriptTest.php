<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts;
use Models\Scripts\Parameters\HelpParameter;
use Models\Scripts\Parameters\VersionParameter;

class DefaultScriptTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("DefaultScriptTest");
	}

	private $title = "Title";
	private $name = "name.py";
	private $htmlId = "script";
	private $mockProject = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}

	public function setUp() {
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getScriptName", "getScriptTitle", "getHtmlId"))
			->getMock();
		$this->object->expects($this->any())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->any())->method("getScriptTitle")->will($this->returnValue($this->title));
		$this->object->expects($this->any())->method("getHtmlId")->will($this->returnValue($this->htmlId));
	}

	/**
	 * @covers \Models\Scripts\DefaultScript::getInitialParameters
	 */
	public function testGetInitialParameters() {
		$this->object->expects($this->exactly(2))->method("getHtmlId")->will($this->returnValue("script"));
		$helpParam = new HelpParameter();
		$helpParam->excludeButAllowIf();
		$versionParam = new VersionParameter($this->object);
		$versionParam->excludeButAllowIf();
		$expected = array("--help" => $helpParam, "--version" => $versionParam);

		$actual = $this->object->getInitialParameters();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::getParameters
	 */
	public function testGetParameters() {
		$expected = array(1, 2, 3);
		$expecteds = array(
			"first_call" => $expected,
			"second_call" => $expected
		);
		$actuals = array();
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getInitialParameters"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getInitialParameters")->will($this->returnValue($expected));

		$actuals['first_call'] = $this->object->getParameters();
		$actuals['second_call'] = $this->object->getParameters();

		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\DefaultScript::renderAsForm
	 */
	public function testRenderAsForm_disabled_zeroParameters() {
		$expected = "<form method=\"POST\"><h4>{$this->title} - {$this->name}</h4>\n" . 
			"<input type=\"hidden\" name=\"step\" value=\"run\" disabled/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->htmlId}\" disabled/>
			<button type=\"submit\" disabled>Run</button>\n</form>";
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getScriptTitle", "getScriptName", "getParameters", "getHtmlId"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getScriptTitle")->will($this->returnValue($this->title));
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->once())->method("getHtmlId")->will($this->returnValue($this->htmlId));
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue(array()));

		$actual = $this->object->renderAsForm(true);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderAsForm
	 */
	public function testRenderAsForm_disabled_oneParameter() {
		$expectedParamForm = "parameter";
		$expected = "<form method=\"POST\"><h4>{$this->title} - {$this->name}</h4>\n" . 
			"{$expectedParamForm}\n" . 
			"<input type=\"hidden\" name=\"step\" value=\"run\" disabled/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->htmlId}\" disabled/>
			<button type=\"submit\" disabled>Run</button>\n</form>";
		$mockParameter = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("renderForForm"))
			->getMock();
		$mockParameter->expects($this->once())->method("renderForForm")->will($this->returnValue($expectedParamForm));
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getScriptTitle", "getScriptName", "getParameters", "getHtmlId"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getScriptTitle")->will($this->returnValue($this->title));
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->once())->method("getHtmlId")->will($this->returnValue($this->htmlId));
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue(array($mockParameter)));

		$actual = $this->object->renderAsForm(true);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderAsForm
	 */
	public function testRenderAsForm_disabled_manyParameters() {
		$expectedParamForm = "parameter";
		$expectedTriggerForm = "trigger";
		$expected = "<form method=\"POST\"><h4>{$this->title} - {$this->name}</h4>\n" . 
			"{$expectedParamForm}\n" . 
			"{$expectedTriggerForm}\n" .  
			"<input type=\"hidden\" name=\"step\" value=\"run\" disabled/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->htmlId}\" disabled/>
			<button type=\"submit\" disabled>Run</button>\n</form>";
		$mockParameter = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("renderForForm"))
			->getMock();
		$mockParameter->expects($this->once())->method("renderForForm")->will($this->returnValue($expectedParamForm));
		$mockTrigger = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("renderForForm"))
			->getMock();
		$mockTrigger->expects($this->once())->method("renderForForm")->will($this->returnValue($expectedTriggerForm));
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getScriptTitle", "getScriptName", "getParameters", "getHtmlId"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getScriptTitle")->will($this->returnValue($this->title));
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->once())->method("getHtmlId")->will($this->returnValue($this->htmlId));
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue(array($mockParameter, $mockTrigger)));

		$actual = $this->object->renderAsForm(true);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderAsForm
	 */
	public function testRenderAsForm_notDisabled_zeroParameters() {
		$expected = "<form method=\"POST\"><h4>{$this->title} - {$this->name}</h4>\n" . 
			"<input type=\"hidden\" name=\"step\" value=\"run\"/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->htmlId}\"/>
			<button type=\"submit\">Run</button>\n</form>" . 
			"<script type=\"text/javascript\">\nvar js_{$this->htmlId} = $('div#form_{$this->htmlId} form');\n" . 
			"</script>\n";
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getScriptTitle", "getScriptName", "getParameters", "getHtmlId"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getScriptTitle")->will($this->returnValue($this->title));
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->exactly(3))->method("getHtmlId")->will($this->returnValue($this->htmlId));
		$this->object->expects($this->exactly(2))->method("getParameters")->will($this->returnValue(array()));

		$actual = $this->object->renderAsForm(false);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderAsForm
	 */
	public function testRenderAsForm_notDisabled_oneParameter() {
		$expectedParamForm = "parameter";
		$expectedParamScript = "script;";
		$expected = "<form method=\"POST\"><h4>{$this->title} - {$this->name}</h4>\n" . 
			"{$expectedParamForm}\n" . 
			"<input type=\"hidden\" name=\"step\" value=\"run\"/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->htmlId}\"/>
			<button type=\"submit\">Run</button>\n</form>" . 
			"<script type=\"text/javascript\">\nvar js_{$this->htmlId} = $('div#form_{$this->htmlId} form');\n{$expectedParamScript}" . 
			"</script>\n";
		$mockParameter = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("renderForForm", "renderFormScript", "isATrigger", "getJsVar"))
			->getMock();
		$mockParameter->expects($this->once())->method("renderForForm")->will($this->returnValue($expectedParamForm));
		$mockParameter->expects($this->once())->method("renderFormScript")->will($this->returnValue($expectedParamScript));
		$mockParameter->expects($this->once())->method("isATrigger")->will($this->returnValue(false));
		$mockParameter->expects($this->never())->method("getJsVar")->will($this->returnValue("js_"));
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getScriptTitle", "getScriptName", "getParameters", "getHtmlId"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getScriptTitle")->will($this->returnValue($this->title));
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->exactly(3))->method("getHtmlId")->will($this->returnValue($this->htmlId));
		$this->object->expects($this->exactly(2))->method("getParameters")->will($this->returnValue(array($mockParameter)));

		$actual = $this->object->renderAsForm(false);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderAsForm
	 */
	public function testRenderAsForm_notDisabled_manyParameters() {
		$expectedParamForm = "parameter";
		$expectedParamScript = "script;";
		$expectedTriggerForm = "trigger";
		$expected = "<form method=\"POST\"><h4>{$this->title} - {$this->name}</h4>\n" . 
			"{$expectedParamForm}\n" . 
			"{$expectedTriggerForm}\n" . 
			"<input type=\"hidden\" name=\"step\" value=\"run\"/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->htmlId}\"/>
			<button type=\"submit\">Run</button>\n</form>" . 
			"<script type=\"text/javascript\">\nvar js_{$this->htmlId} = $('div#form_{$this->htmlId} form');\n{$expectedParamScript}" .
			"{$expectedParamScript}js_.change();" . 
			"</script>\n";
		$mockParameter = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("renderForForm", "renderFormScript", "isATrigger", "getJsVar"))
			->getMock();
		$mockParameter->expects($this->once())->method("renderForForm")->will($this->returnValue($expectedParamForm));
		$mockParameter->expects($this->once())->method("renderFormScript")->will($this->returnValue($expectedParamScript));
		$mockParameter->expects($this->once())->method("isATrigger")->will($this->returnValue(false));
		$mockParameter->expects($this->never())->method("getJsVar")->will($this->returnValue("js_"));
		$mockTrigger = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("renderForForm", "renderFormScript", "isATrigger", "getJsVar"))
			->getMock();
		$mockTrigger->expects($this->once())->method("renderForForm")->will($this->returnValue($expectedTriggerForm));
		$mockTrigger->expects($this->once())->method("renderFormScript")->will($this->returnValue($expectedParamScript));
		$mockTrigger->expects($this->once())->method("isATrigger")->will($this->returnValue(true));
		$mockTrigger->expects($this->once())->method("getJsVar")->will($this->returnValue("js_"));
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getScriptTitle", "getScriptName", "getParameters", "getHtmlId"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getScriptTitle")->will($this->returnValue($this->title));
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->exactly(3))->method("getHtmlId")->will($this->returnValue($this->htmlId));
		$this->object->expects($this->exactly(2))->method("getParameters")->will($this->returnValue(array($mockParameter, $mockTrigger)));

		$actual = $this->object->renderAsForm(false);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\DefaultScript::getJsVar
	 */
	public function testGetJsVar() {
		$expected = "js_" . $this->htmlId;
		$this->object->expects($this->once())->method("getHtmlId")->will($this->returnValue($this->htmlId));

		$actual = $this->object->getJsVar();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\DefaultScript::acceptInput
	 */
	public function testAcceptInput_zeroParameters() {
		$expectedParameters = array();
		$expected = NULL;
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue($expectedParameters));

		$actual = $this->object->acceptInput(array());

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::acceptInput
	 */
	public function testAcceptInput_zeroErrors() {
		$expected = NULL;
		$mockParameter = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("acceptInput"))
			->getMock();
		$mockParameter->expects($this->exactly(2))->method("acceptInput");
		$expectedParameters = array($mockParameter, $mockParameter);
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue($expectedParameters));

		$actual = $this->object->acceptInput(array());

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::acceptInput
	 */
	public function testAcceptInput_oneErrorOneNonError() {
		$expected = "There were some problems with the parameters you submitted:<ul><li>message</li></ul>\n";
		$mockParameter = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("acceptInput"))
			->getMock();
		$mockParameter->expects($this->once())->method("acceptInput");
		$mockParameterFailer = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("acceptInput"))
			->getMock();
		$mockParameterFailer->expects($this->once())->method("acceptInput")->will($this->throwException(new ScriptException("message")));
		$expectedParameters = array($mockParameter, $mockParameterFailer);
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue($expectedParameters));
		try {

			$this->object->acceptInput(array());

			$this->fail("acceptInput should have thrown an exception");
		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::acceptInput
	 */
	public function testAcceptInput_manyErrors() {
		$expected = "There were some problems with the parameters you submitted:<ul><li>message</li><li>message</li></ul>\n";
		$mockParameterFailer = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("acceptInput"))
			->getMock();
		$mockParameterFailer->expects($this->exactly(2))->method("acceptInput")->will($this->throwException(new ScriptException("message")));
		$expectedParameters = array($mockParameterFailer, $mockParameterFailer);
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue($expectedParameters));
		try {

			$this->object->acceptInput(array());

			$this->fail("acceptInput should have thrown an exception");
		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\DefaultScript::renderCommand
	 */
	public function testRenderCommand_zeroParameters() {
		$expected = $this->name . " ";
		$expectedParameters = array();
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters", "getScriptName"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue($expectedParameters));

		$actual = $this->object->renderCommand();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderCommand
	 */
	public function testRenderCommand_oneParameter() {
		$expectedParamValue = "--name='value'";
		$mockParameter = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("renderForOperatingSystem"))
			->getMock();
		$mockParameter->expects($this->once())->method("renderForOperatingSystem")->will($this->returnValue($expectedParamValue));
		$expected = $this->name . " " . $expectedParamValue . " ";
		$expectedParameters = array($mockParameter);
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters", "getScriptName"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue($expectedParameters));

		$actual = $this->object->renderCommand();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\DefaultScript::renderCommand
	 */
	public function testRenderCommand_manyParameters() {
		$expectedParamValue = "--name='value'";
		$mockParameter = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("renderForOperatingSystem"))
			->getMock();
		$mockParameter->expects($this->exactly(2))->method("renderForOperatingSystem")->will($this->returnValue($expectedParamValue));
		$expected = $this->name . " " . $expectedParamValue . " " . $expectedParamValue . " ";
		$expectedParameters = array($mockParameter, $mockParameter);
		$this->object = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->setConstructorArgs(array($this->mockProject))
			->setMethods(array("getParameters", "getScriptName"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->once())->method("getParameters")->will($this->returnValue($expectedParameters));

		$actual = $this->object->renderCommand();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\DefaultScript::renderVersionCommand
	 */
	public function testRenderVersionCommand() {
		$expected = $this->name . " --version";
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));

		$actual = $this->object->renderVersionCommand();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\DefaultScript::renderHelp
	 */
	public function testRenderHelp() {
		$expected = "<p><strong>{$this->title} - {$this->name}</strong></p>File: views/script.html\n";
		$this->object->expects($this->once())->method("getScriptTitle")->will($this->returnValue($this->title));
		$this->object->expects($this->once())->method("getScriptName")->will($this->returnValue($this->name));
		$this->object->expects($this->once())->method("getHtmlId")->will($this->returnValue($this->htmlId));

		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}
}
