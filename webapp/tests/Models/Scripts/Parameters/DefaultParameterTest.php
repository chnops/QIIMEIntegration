<?php

namespace Models\Scripts\Parameters;

class DefaultParameterTest extends \PHPUnit_Framework_TestCase {

	private $object;
	private $name = "--file_path";
	private $value = "./file/path.ext";
	private $mockScript = NULl;

	public static function setUpBeforeClass() {
		error_log("DefaultParameterTest");
	}

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$stubGetter = new \Stubs\StubGetter();
		$this->mockScript = $stubGetter->getScript();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue("js_script"));
	}

	public function setUp() {
		$this->object = new DefaultParameter($this->name, $this->value);
	}

	/**
	 * @test
	 * @covers DefaultParmeter::getValue
	 * @covers DefaultParmeter::getName
	 * @covers DefaultParmeter::isRequired
	 */
	public function testGetters() {
		$this->assertEquals($this->name, $this->object->getName());
		$this->assertEquals($this->value, $this->object->getValue());
	}

	/**
	 * @test
	 * @covers DefaultParmeter::setValue
	 * @covers DefaultParmeter::setName
	 * @covers DefaultParmeter::setIsRequired
	 */
	public function testSetters() {
		$newName = "--new_file_path";
		$this->object->setName($newName);
		$newValue = "./new/file/path.ext";
		$this->object->setValue($newValue);
		$this->assertEquals($newName, $this->object->getName());
		$this->assertEquals($newValue, $this->object->getValue());
	}

	/**
	 * @test
	 * @covers DefaultParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {
		$expectedString = $this->name . "='" . $this->value . "'";
		$this->assertEquals($expectedString, $this->object->renderForOperatingSystem());
	}

	/**
	 * @test
	 * @covers DefaultParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expectedString = "<label for=\"{$this->name}\">{$this->name}<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\"/></label>";
		$this->assertEquals($expectedString, $this->object->renderForForm($disabled = false, $this->mockScript));
	}
}
