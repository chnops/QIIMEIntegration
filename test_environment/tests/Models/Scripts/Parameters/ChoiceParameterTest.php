<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\Parameters;

class ChoiceParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ChoiceParameterTest");
	}

	private $name = "--choice_param";
	private $default = "two";
	private $options = array("one", "two", "three");
	private $scriptJsVar = "js_script";
	private $mockScript = NULL;
	private $paramHelp = "<a class=\"param_help\" id=\"js_script_choice_param\">&amp;</a>";
	private $object;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getJsVar"))
			->getMockForAbstractClass();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue($this->scriptJsVar));
	}

	public function setUp() {
		$this->object = new ChoiceParameter($this->name, $this->default, $this->options);
	}

	/**
	 * @covers ChoiceParameter::__construct
	 */
	public function testConstructor() {
		$expecteds = array(
			"name" => $this->name,
			"value" => $this->default,
			"options" => $this->options,
		);
		$actuals = array();

		$this->object = new ChoiceParameter($this->name, $this->default, $this->options);
		
		$actuals['name'] = $this->object->getName();
		$actuals['value'] = $this->object->getValue();
		$actuals['options'] = $this->object->getOptions();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers ChoiceParameter::isValidValue
	 */
	public function testIsValueValid_valueIsEmpty() {
		$expected = true;

		$actual = $this->object->isValueValid(false);

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers ChoiceParameter::isValidValue
	 */
	public function testIsValueValid_valueIsTrue() {
		$expected = false;

		$actual = $this->object->isValueValid(true);

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers ChoiceParameter::isValidValue
	 */
	public function testIsValueValid_valueIsValid() {
		$expected = true;

		$actual = $this->object->isValueValid($this->options[0]);

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers ChoiceParameter::isValidValue
	 */
	public function testIsValueValid_valueIsNotValid() {
		$expected = false;

		$actual = $this->object->isValueValid("not_" . $this->options[0]);

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers ChoiceParameter::renderForForm
	 */
	public function testRenderForForm_disabled_zeroOptions() {
		$expected = "<label for=\"{$this->name}\">{$this->name} {$this->paramHelp}
			<select name=\"{$this->name}\" disabled>\n" .
			"</select></label>\n";
		$this->object->setOptions(array());

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers ChoiceParameter::renderForForm
	 */
	public function testRenderForForm_disabled_manyOptions() {
		$expected = "<label for=\"{$this->name}\">{$this->name} {$this->paramHelp}
			<select name=\"{$this->name}\" disabled>\n" .
			"<option value=\"one\">one</option>\n" .
			"<option value=\"two\" selected>two</option>\n" .
			"<option value=\"three\">three</option>\n" .
			"</select></label>\n";

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers ChoiceParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_zeroOptions() {
		$expected = "<label for=\"{$this->name}\">{$this->name} {$this->paramHelp}
			<select name=\"{$this->name}\">\n" .
			"</select></label>\n";
		$this->object->setOptions(array());

		$actual = $this->object->renderForForm($disabled = false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers ChoiceParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_manyOptions() {
		$expected = "<label for=\"{$this->name}\">{$this->name} {$this->paramHelp}
			<select name=\"{$this->name}\">\n" .
			"<option value=\"one\">one</option>\n" .
			"<option value=\"two\" selected>two</option>\n" .
			"<option value=\"three\">three</option>\n" .
			"</select></label>\n";

		$actual = $this->object->renderForForm($disabled = false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers ChoiceParameter::getOptions
	 */
	public function testGetOptions() {
		$expected = $this->options;

		$actual = $this->object->getOptions();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers ChoiceParameter::setOptions
	 */
	public function testSetOptions() {
		$expected = array(1, 2, 3);

		$this->object->setOptions($expected);

		$actual = $this->object->getOptions();
		$this->assertEquals($expected, $actual);
	}
}
