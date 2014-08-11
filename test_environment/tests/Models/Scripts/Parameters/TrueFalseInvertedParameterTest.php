<?php

namespace Models\Scripts\Parameters;

class TrueFalseInvertedParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("TrueFalseInvertedParameterTest");
	}

	private $mockScript = NULL;
	private $name = "--name";
	private $object;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getJsVar"))
			->getMockForAbstractClass();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue("js_script"));
	}

	public function setUp() {
		$this->object = new TrueFalseInvertedParameter($this->name);
	}

	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseInvertedParameter::__construct
	 */
	public function testConstructor() {
		$expecteds = array(
			"name" => $this->name,
			"value" => true,
		);
		$actuals = array();

		$this->object = new TrueFalseInvertedParameter($this->name);

		$actuals['name'] = $this->object->getName();
		$actuals['value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseInvertedParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_valueTrue() {
		$expected = $this->name;
		$this->object->setValue(false);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseInvertedParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_valueFalse() {
		$expected = "";
		$this->object->setValue(true);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
}
