<?php

namespace Models\Scripts\Parameters;

class TrueFalseInvertedParameterTest extends \PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
		error_log("TrueFalseInvertedParameterTest");
	}

	private $name = "--true_false_inverted";
	private $object;
	private $mockScript = NULL;

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$stubGetter = new \Stubs\StubGetter();
		$this->mockScript = $stubGetter->getScript();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue("js_script"));
	}

	public function setUp() {
		$this->object = new TrueFalseInvertedParameter($this->name);
	}

	/**
	 * @test
	 * @covers TrueFalseInvertedParameter::__construct
	 */
	public function testConstructor() {
		$this->assertTrue($this->object->getValue());
		$this->object = new TrueFalseInvertedParameter($this->name, FALSE);
		$this->assertTrue($this->object->getValue());
	}

	/**
	 * @test
	 * @covers TrueFalseInvertedParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {
		$this->assertEmpty($this->object->renderForOperatingSystem());
		$this->object->setValue(FALSE);
		$this->assertEquals($this->name, $this->object->renderForOperatingSystem());
	}

	/**
	 * @test
	 * @covers TrueFalseInvertedParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expectedIfTrue = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\" checked/> {$this->name}</label>";
		$this->assertEquals($expectedIfTrue, $this->object->renderForForm($disabled = false, $this->mockScript));
		$this->object->setValue(FALSE);
		$expectedIfFalse = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\"/> {$this->name}</label>";
		$this->assertEquals($expectedIfFalse, $this->object->renderForForm($disabled = false, $this->mockScript));
	}
}
