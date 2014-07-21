<?php

namespace Models\Scripts\Parameters;

class ChoiceParameterTest extends \PHPUnit_Framework_TestCase {

	private $name = "--choice_param";
	private $defaultValue = "two";
	private $options = array("one", "two", "three");

	private $parameter;

	public static function setUpBeforeClass() {
		error_log("ChoiceParameterTest");
	}

	public function setUp() {
		$this->parameter = new ChoiceParameter($this->name, $this->defaultValue, $this->options);
	}

	/**
	 * @test
	 * @covers ChoiceParameter::__construct
	 */
	public function testConstructor() {
		$this->assertEquals($this->name, $this->parameter->getName());
		$this->assertEquals($this->defaultValue, $this->parameter->getValue());
	}

	/**
	 * @test
	 * @covers ChoiceParameter::isValidValue
	 */
	public function testIsValueValid() {
		$this->assertTrue($this->parameter->isValueValid());
		$this->parameter->setValue("one");
		$this->assertTrue($this->parameter->isValueValid());
		$this->parameter->setValue("three");
		$this->assertTrue($this->parameter->isValueValid());
	}

	/**
	 * @test
	 * @covers ChoiceParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expectedOutput = "<label for=\"{$this->name}\">{$this->name}<select name=\"{$this->name}\">\n";
		$expectedOutput .= "<option value=\"one\">one</option>\n";
		$expectedOutput .= "<option value=\"two\" selected>two</option>\n";
		$expectedOutput .= "<option value=\"three\">three</option>\n";
		$expectedOutput .= "</select></label>\n";
		$this->assertEquals($expectedOutput, $this->parameter->renderForForm());
	}
}
