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
		$expected = false;

		$actual = $this->object->isDir();

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::setIsDir
	 */
	public function testSetIsDir_false() {
		$expected = false;

		$this->object->setIsDir(0);

		$actual = $this->object->isDir();
		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::setIsDir
	 */
	public function testSetIsDir_true() {
		$expected = true;

		$this->object->setIsDir("yes");

		$actual = $this->object->isDir();
		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::isValueValid
	 */
	public function testIsValueValid_valueIsValid() {
		$expected = true;

		$actual = $this->object->isValueValid("file_name_without_'double'_quotes");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::isValueValid
	 */
	public function testIsValueValid_valueIsNotValid() {
		$expected = false;

		$actual = $this->object->isValueValid("file_name_with_\"double\"_quotes");

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\NewFileParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_isDir_valueFalse() {
		$expected = "";
		$this->object = new NewFileParameter($this->name, $value = false, $isDir = true);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertSame($expected, $actual);
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
