<?php

namespace Models\Scripts\Parameters;

class NewFileParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("NewFileParameterTest");
	}

	private $name = "name";
	private $value = "value";
	private $object;
	public function setUp() {
		$this->object = new NewFileParameter($this->name, $this->value);
	}

	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::__constructor
	 */
	public function testConstructor_isNotDir() {
		$expecteds = array(
			"name" => $this->name,
			"value" => $this->value,
			"is_dir" => false,
		);
		$actuals = array();

		$this->object = new NewFileParameter($this->name, $this->value, $isDir = false);

		$actuals['name'] = $this->object->getName();
		$actuals['value'] = $this->object->getValue();
		$actuals['is_dir'] = $this->object->isDir();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::__constructor
	 */
	public function testConstructor_isDir() {
		$expecteds = array(
			"name" => $this->name,
			"value" => $this->value,
			"is_dir" => true,
		);
		$actuals = array();

		$this->object = new NewFileParameter($this->name, $this->value, $isDir = true);

		$actuals['name'] = $this->object->getName();
		$actuals['value'] = $this->object->getValue();
		$actuals['is_dir'] = $this->object->isDir();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::isDir
	 */
	public function testIsDir() {

		$actual = $this->object->isDir();

		$this->assertFalse($actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::setIsDir
	 */
	public function testSetIsDir_false() {
		$isDir = 0;

		$this->object->setIsDir($isDir);

		$actual = $this->object->isDir();
		$this->assertFalse($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::setIsDir
	 */
	public function testSetIsDir_true() {
		$isDir = "yes";

		$this->object->setIsDir($isDir);

		$actual = $this->object->isDir();
		$this->assertTrue($actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::isValueValid
	 */
	public function testIsValueValid_valueIsValid() {
		$value = "file_name_without_'double'_quotes";

		$actual = $this->object->isValueValid($value);

		$this->assertTrue($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::isValueValid
	 */
	public function testIsValueValid_valueIsNotValid() {
		$value = "file_name_with_\"double\"_quotes";

		$actual = $this->object->isValueValid($value);

		$this->assertFalse($actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_isDir_valueFalse() {
		$this->object = new NewFileParameter($this->name, $value = false, $isDir = true);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_isDir_valueEmpty() {
		$expected = ".";
		$this->object = new NewFileParameter($this->name, $value = "", $isDir = true);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_isDir_valueNotEmpty() {
		$expectedGenerated = new DefaultParameter($this->name, $this->value);
		$expected = $expectedGenerated->renderForOperatingSystem();
		$this->object = new NewFileParameter($this->name, $this->value, $isDir = true);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_isNotDir() {
		$expectedGenerated = new DefaultParameter($this->name, $this->value);
		$expected = $expectedGenerated->renderForOperatingSystem();

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
}
