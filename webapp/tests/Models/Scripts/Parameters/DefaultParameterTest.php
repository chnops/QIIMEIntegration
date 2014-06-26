<?php

namespace Models\Scripts\Parameters;

class DefaultParameterTest extends \PHPUnit_Framework_TestCase {

	private $parameter;
	private $name = "--file_path";
	private $value = "./file/path.ext";

	public static function setUpBeforeClass() {
		error_log("DefaultParameterTest");
	}

	public function setUp() {
		$this->parameter = new DefaultParameter($this->name, $this->value);
	}

	/**
	 * @test
	 * @covers DefaultParmeter::getValue
	 * @covers DefaultParmeter::getName
	 * @covers DefaultParmeter::isRequired
	 */
	public function testGetters() {
		$this->assertEquals($this->name, $this->parameter->getName());
		$this->assertEquals($this->value, $this->parameter->getValue());
	}

	/**
	 * @test
	 * @covers DefaultParmeter::setValue
	 * @covers DefaultParmeter::setName
	 * @covers DefaultParmeter::setIsRequired
	 */
	public function testSetters() {
		$newName = "--new_file_path";
		$this->parameter->setName($newName);
		$newValue = "./new/file/path.ext";
		$this->parameter->setValue($newValue);
		$this->assertEquals($newName, $this->parameter->getName());
		$this->assertEquals($newValue, $this->parameter->getValue());
	}

	/**
	 * @test
	 * @covers DefaultParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {
		$expectedString = $this->name . "='" . $this->value . "'";
		$this->assertEquals($expectedString, $this->parameter->renderForOperatingSystem());
	}

	/**
	 * @test
	 * @covers DefaultParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expectedString = "<label for=\"{$this->name}\">{$this->name}<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\"/></label>";
		$this->assertEquals($expectedString, $this->parameter->renderForForm());
	}
}
