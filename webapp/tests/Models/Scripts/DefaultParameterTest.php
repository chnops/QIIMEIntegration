<?php

namespace Models\Scripts;

class DefaultParameterTest extends \PHPUnit_Framework_TestCase {

	private $parameter;
	private $name = "--file_path";
	private $value = "./file/path.ext";
	private $isRequired = False;

	public function setUp() {
		$this->parameter = new DefaultParameter($this->name, $this->value, $this->isRequired);
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
		$this->assertEquals($this->isRequired, $this->parameter->isRequired());
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
		$isRequiredNow = true;
		$this->parameter->setIsRequired($isRequiredNow);
		$this->assertEquals($newName, $this->parameter->getName());
		$this->assertEquals($newValue, $this->parameter->getValue());
		$this->assertEquals($isRequiredNow, $this->parameter->isRequired());
	}

	/**
	 * @test
	 * @covers DefaultParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {
		$expectedString = $this->name . "=" . $this->value;
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