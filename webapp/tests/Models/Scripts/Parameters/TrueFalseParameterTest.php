<?php

namespace Models\Scripts\Parameters;

class TrueFalseParameterTest extends \PHPUnit_Framework_TestCase {

	private $name = "--true_false";
	private $object;
	private $mockScript = NULL;

	public static function setUpBeforeClass() {
		error_log("TrueFalseParameterTest");
	}

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$stubGetter = new \Stubs\StubGetter();
		$this->mockScript = $stubGetter->getScript();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue("js_script"));
	}

	public function setUp() {
		$this->object = new TrueFalseParameter($this->name);
	}

	/**
	 * @test
	 * @covers TrueFalseParameter::__construct
	 */
	public function testConstructor() {
		$this->assertFalse($this->object->getValue());
		$this->object = new TrueFalseParameter($this->name, TRUE);
		$this->assertFalse($this->object->getValue());
	}

	/**
	 * @test
	 * @covers TrueFalseParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {
		$this->assertEmpty($this->object->renderForOperatingSystem());
		$this->object->setValue(TRUE);
		$this->assertEquals($this->name, $this->object->renderForOperatingSystem());
	}

	/**
	 * @test
	 * @covers TrueFalseParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expectedIfFalse = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\"/> {$this->name}</label>";
		$this->assertEquals($expectedIfFalse, $this->object->renderForForm($disabled = false, $this->mockScript));
		$this->object->setValue(TRUE);
		$expectedIfTrue = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\" checked/> {$this->name}</label>";
		$this->assertEquals($expectedIfTrue, $this->object->renderForForm($disabled = false, $this->mockScript));
	}
}
