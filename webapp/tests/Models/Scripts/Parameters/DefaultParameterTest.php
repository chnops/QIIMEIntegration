<?php

namespace Models\Scripts\Parameters;

class DefaultParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("DefaultParameterTest");
	}

	private $name = "--file_path";
	private $value = "./file/path.ext";
	private $scriptJsVar = "js_script";
	private $mockScript = NULL;
	private $paramHelp = "<a class=\"param_help\" id=\"js_script_file_path\">&amp;</a>";
	private $object;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getJsVar"))
			->getMockForAbstractClass();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue($this->scriptJsVar));
	}

	public function setUp() {
		$this->object = new DefaultParameter($this->name, $this->value);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::__construct
	 */
	public function testConstructor() {
		$expecteds = array(
			'name' => $this->name,
			'value' => $this->value,
		);
		$actuals = array();


		$actuals['name'] = $this->object->getName();
		$actuals['value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getValue
	 */
	public function testGetValue() {
		$expected = $this->value;

		$actual = $this->object->getValue();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setValue
	 */
	public function testSetValue_validValue() {
		$expected = "new value";

		$this->object->setValue($expected);

		$actual = $this->object->getValue();
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setValue
	 * @expectedException \Models\Scripts\ScriptException
	 */
	public function testSetValue_inValidValue() {
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("isValueValid"))
			->getMock();
		$this->object->expects($this->once())->method("isValueValid")->will($this->returnValue(false));

		$this->object->setValue("anyValue");

		$this->fail("setValue should have thrown an exception");
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getName
	 */
	public function testGetName() {
		$expected = $this->name;

		$actual = $this->object->getName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setName
	 */
	public function testSetName() {
		$expected = "new name";

		$this->object->setName($expected);

		$actual = $this->object->getName();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::isValueValid
	 */
	public function testIsValueValid() {
		
		$actual = $this->object->isValueValid("anyValueAtAll");

		$this->assertTrue($actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_noValue() {
		$expected = "";
		$this->object->setValue(false);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_twoLengthName() {
		$twoLengthName = "-n";
		$expected = $twoLengthName . " '" . $this->value . "'";
		$this->object->setName($twoLengthName);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_nonTwoLengthName() {
		$expected = $this->name . "='" . $this->value . "'";

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_valueIsEscaped() {
		$escapableValue = "val'ue";
		$expected = $this->name . "='val'\''ue'";
		$this->object->setValue($escapableValue);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderForForm
	 */
	public function testRenderForForm_disabled() {
		$expected = "<label for=\"{$this->name}\">{$this->name} {$this->paramHelp}
			<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\" disabled/></label>";

		$actual = $this->object->renderForForm($disable = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled() {
		$expected = "<label for=\"{$this->name}\">{$this->name} {$this->paramHelp}
			<input type=\"text\" name=\"{$this->name}\" value=\"{$this->value}\"/></label>";

		$actual = $this->object->renderForForm($disabled = false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderFormScript
	 */
	public function testRenderFormScript() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::requireIf
	 */
	public function testRequireIf() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::dismissIf
	 */
	public function testDismissIf() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::excludeButAllowIf
	 */
	public function testExcludeButAllowIf() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::excludeIf
	 */
	public function testExcludeIf() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::linkTo
	 */
	public function testLinkTo() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::isATrigger
	 */
	public function testIsATrigger() {
		$this->markTestIncomplete();
	}
}
