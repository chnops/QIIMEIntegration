<?php

namespace Models\Scripts\Parameters;

class TrueFalseParameterTest extends \PHPUnit_Framework_TestCase {

	private $name = "--true_false";
	private $parameter;

	public static function setUpBeforeClass() {
		error_log("TrueFalseParameterTest");
	}

	public function setUp() {
		$this->parameter = new TrueFalseParameter($this->name);
	}

	/**
	 * @test
	 * @covers TrueFalseParameter::__construct
	 */
	public function testConstructor() {
		$this->assertFalse($this->parameter->getValue());
		$this->parameter = new TrueFalseParameter($this->name, TRUE);
		$this->assertFalse($this->parameter->getValue());
	}

	/**
	 * @test
	 * @covers TrueFalseParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {
		$this->assertEmpty($this->parameter->renderForOperatingSystem());
		$this->parameter->setValue(TRUE);
		$this->assertEquals($this->name, $this->parameter->renderForOperatingSystem());
	}

	/**
	 * @test
	 * @covers TrueFalseParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expectedIfFalse = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\"/> {$this->name}</label>";
		$this->assertEquals($expectedIfFalse, $this->parameter->renderForForm());
		$this->parameter->setValue(TRUE);
		$expectedIfTrue = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\" checked/> {$this->name}</label>";
		$this->assertEquals($expectedIfTrue, $this->parameter->renderForForm());
	}
}
