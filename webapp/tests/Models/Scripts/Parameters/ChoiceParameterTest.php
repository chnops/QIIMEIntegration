<?php

namespace Models\Scripts\Parameters;

class ChoiceParameterTest extends \PHPUnit_Framework_TestCase {

	private $name = "--choice_param";
	private $defaultValue = "two";
	private $options = array("one", "two", "three");
	private $mockScript = NULL;

	private $object;

	public static function setUpBeforeClass() {
		error_log("ChoiceParameterTest");
	}

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$stubGetter = new \Stubs\StubGetter();
		$this->mockScript = $stubGetter->getScript();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue("js_script"));
	}

	public function setUp() {
		$this->object = new ChoiceParameter($this->name, $this->defaultValue, $this->options);
	}

	/**
	 * @test
	 * @covers ChoiceParameter::__construct
	 */
	public function testConstructor() {
		$this->assertEquals($this->name, $this->object->getName());
		$this->assertEquals($this->defaultValue, $this->object->getValue());
	}

	/**
	 * @test
	 * @covers ChoiceParameter::isValidValue
	 */
	public function testIsValueValid() {
		$this->assertTrue($this->object->isValueValid(""));
		$this->object->setValue("");
		$this->assertTrue($this->object->isValueValid("one"));
		$this->object->setValue("one");
		$this->assertTrue($this->object->isValueValid("three"));
		$this->object->setValue("three");
	}

	/**
	 * @test
	 * @covers ChoiceParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expectedOutput = "<label for=\"{$this->name}\">{$this->name}<select name=\"{$this->name}\">\n";
		$expectedOutput .= "<option value=\"one\">one</option>\n";
		$expectedOutput .= "<option value=\"two\" selected>two</option>\n";
		$expectedOutput .= "<option value=\"three\">three</option>\n";
		$expectedOutput .= "</select></label>\n";
		$this->assertEquals($expectedOutput, $this->object->renderForForm($disabled = false, $this->mockScript));
	}
}
