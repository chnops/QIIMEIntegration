<?php

namespace Models\Scripts\Parameters;

class TrueFalseInvertedParameterTest extends \PHPUnit_Framework_TestCase {

	private $name = "--true_false_inverted";
	private $parameter;

	public static function setUpBeforeClass() {
		error_log("TrueFalseInvertedParameterTest");
	}

	public function setUp() {
		$this->parameter = new TrueFalseInvertedParameter($this->name);
	}

	/**
	 * @test
	 * @covers TrueFalseInvertedParameter::__construct
	 */
	public function testConstructor() {
		$this->assertTrue($this->parameter->getValue());
		$this->parameter = new TrueFalseInvertedParameter($this->name, FALSE);
		$this->assertTrue($this->parameter->getValue());
	}

	/**
	 * @test
	 * @covers TrueFalseInvertedParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {
		$this->assertEmpty($this->parameter->renderForOperatingSystem());
		$this->parameter->setValue(FALSE);
		$this->assertEquals($this->name, $this->parameter->renderForOperatingSystem());
	}

	/**
	 * @test
	 * @covers TrueFalseInvertedParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expectedIfTrue = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\" checked/> {$this->name}</label>";
		$this->assertEquals($expectedIfTrue, $this->parameter->renderForForm());
		$this->parameter->setValue(FALSE);
		$expectedIfFalse = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\"/> {$this->name}</label>";
		$this->assertEquals($expectedIfFalse, $this->parameter->renderForForm());
	}
}
