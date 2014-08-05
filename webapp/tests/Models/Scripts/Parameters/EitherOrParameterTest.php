<?php

namespace Models\Scripts\Parameter;
use Models\Scripts\ScriptException;

class EitherOrParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("EitherOrParameterTest");
	}

	private $defaultName = "--defaultName";
	private $defaultOperatingSystem = "--defaultName='defaultValue'";
	private $defaultForm = "<input name=\"--defaultName\" value=\"defaultValue\"/>";
	private $defaultScript = "defaultScript();";
	private $mockDefault = NULL;
	private $alternativeName = "--alternativeName";
	private $alternativeOperatingSystem = "--alternativeName='alternativeValue'";
	private $alternativeForm = "<input name=\"--alternativeName\" value=\"alternativeValue\"/>";
	private $alternativeScript = "alternativeScript();";
	private $mockAlternative = NULL;
	private $scriptJsVar = "js_script";
	private $mockScript = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockDefault = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("getName", "renderForOperatingSystem", "renderForForm", "renderFormScript"))
			->getMock();
		$this->mockDefault->expects($this->any())->method("getName")->will($this->returnValue($this->defaultName));
		$this->mockDefault->expects($this->any())->method("renderForOperatingSystem")->will($this->returnValue($this->defaultOperatingSystem));
		$this->mockDefault->expects($this->any())->method("renderForForm")->will($this->returnValue($this->defaultForm));
		$this->mockDefault->expects($this->any())->method("renderFormScript")->will($this->returnValue($this->defaultScript));

		$this->mockAlternative = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("getName", "renderForOperatingSystem", "renderForForm", "renderFormScript"))
			->getMock();
		$this->mockAlternative->expects($this->any())->method("getName")->will($this->returnValue($this->alternativeName));
		$this->mockAlternative->expects($this->any())->method("renderForOperatingSystem")->will($this->returnValue($this->alternativeOperatingSystem));
		$this->mockAlternative->expects($this->any())->method("renderForForm")->will($this->returnValue($this->alternativeForm));
		$this->mockAlternative->expects($this->any())->method("renderFormScript")->will($this->returnValue($this->alternativeScript));

		$this->mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getJsVar"))
			->getMockForAbstractClass();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue($this->scriptJsVar));
	}
	public function setUp() {
		$this->object = new \Models\Scripts\Parameters\EitherOrParameter($this->mockDefault, $this->mockAlternative);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::__construct
	 */
	public function testConstruct_noDisplayName() {
		$expecteds = array(
			"default" => $this->mockDefault,
			"alternative" => $this->mockAlternative,
			"name" => "__" . $this->mockDefault->getName() . "__" . $this->mockAlternative->getName() . "__",
			"display_name" => $this->mockDefault->getName() . " or " . $this->mockAlternative->getName(),
		);
		$actuals = array();

		$this->object = new \Models\Scripts\Parameters\EitherOrParameter($this->mockDefault, $this->mockAlternative, $expectedName = "");

		$actuals['default'] = $this->object->getDefault();
		$actuals['alternative'] = $this->object->getAlternative();
		$actuals['name'] = $this->object->getName();
		$actuals['display_name'] = $this->object->getDisplayName();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::__construct
	 */
	public function testConstruct_displayName() {
		$expectedName = "display name";
		$expecteds = array(
			"default" => $this->mockDefault,
			"alternative" => $this->mockAlternative,
			"name" => "__" . $this->mockDefault->getName() . "__" . $this->mockAlternative->getName() . "__",
			"display_name" => $expectedName,
		);
		$actuals = array();

		$this->object = new \Models\Scripts\Parameters\EitherOrParameter($this->mockDefault, $this->mockAlternative, $expectedName);

		$actuals['default'] = $this->object->getDefault();
		$actuals['alternative'] = $this->object->getAlternative();
		$actuals['name'] = $this->object->getName();
		$actuals['display_name'] = $this->object->getDisplayName();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::getDefault
	 */
	public function testGetDefault() {
		$expected = $this->mockDefault;

		$actual = $this->object->getDefault();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::setDefault
	 */
	public function testSetDefault() {
		$expected = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->object->setDefault($expected);

		$actual = $this->object->getDefault();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::getAlternative
	 */
	public function testGetAlternative() {
		$expected = $this->mockAlternative;

		$actual = $this->object->getAlternative();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::setAlternative
	 */
	public function testSetAlternative() {
		$expected = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->object->setAlternative($expected);

		$actual = $this->object->getAlternative();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::getDisplayName
	 */
	public function testGetDisplayName() {
		$expected = $this->defaultName . " or " . $this->alternativeName;

		$actual = $this->object->getDisplayName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::setDisplayName
	 */
	public function testSetDisplayName() {
		$expected = "new name";

		$this->object->setDisplayName($expected);

		$actual = $this->object->getDisplayName();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_noValue() {
		$expected = "";
		$value = false;
		$this->object->setValue($value);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_defaultValue() {
		$expected = $this->defaultOperatingSystem;
		$value = $this->defaultName;
		$this->object->setValue($value);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_alternativeValue() {
		$expected = $this->alternativeOperatingSystem;
		$value = $this->alternativeName;
		$this->object->setValue($value);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::renderForForm
	 */
	public function testRenderForForm_disabled_noValueChecked() {
		$expectedJsVar = "js_script_param";
		$expectedName = "__{$this->defaultName}__{$this->alternativeName}__";
		$expected = "<table class=\"either_or\"><tr><td colspan=\"2\"><label for=\"{$expectedName}\">{$this->defaultName} or {$this->alternativeName}
			<a class=\"param_help\" id=\"{$expectedJsVar}\">&amp;</a><br/>" . 
			"<input type=\"radio\" name=\"{$expectedName}\" value=\"\" checked disabled>Neither</label></td></tr>" . 
			"<tr>" . 
			"<td><label for=\"{$expectedName}\"><input type=\"radio\" name=\"{$expectedName}\" value=\"{$this->defaultName}\" disabled>
				{$this->defaultForm}</label></td>" . 
				"<td><label for=\"{$expectedName}\"><input type=\"radio\" name=\"{$expectedName}\" value=\"{$this->alternativeName}\" disabled>
				{$this->alternativeForm}</label></td>" . 
			"</tr></table>";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($this->mockDefault, $this->mockAlternative))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::renderForForm
	 */
	public function testRenderForForm_disabled_defaultChecked() {
		$expectedJsVar = "js_script_param";
		$expectedName = "__{$this->defaultName}__{$this->alternativeName}__";
		$expected = "<table class=\"either_or\"><tr><td colspan=\"2\"><label for=\"{$expectedName}\">{$this->defaultName} or {$this->alternativeName}
			<a class=\"param_help\" id=\"{$expectedJsVar}\">&amp;</a><br/>" . 
			"<input type=\"radio\" name=\"{$expectedName}\" value=\"\" disabled>Neither</label></td></tr>" . 
			"<tr>" . 
			"<td><label for=\"{$expectedName}\"><input type=\"radio\" name=\"{$expectedName}\" value=\"{$this->defaultName}\" checked disabled>
				{$this->defaultForm}</label></td>" . 
				"<td><label for=\"{$expectedName}\"><input type=\"radio\" name=\"{$expectedName}\" value=\"{$this->alternativeName}\" disabled>
				{$this->alternativeForm}</label></td>" . 
			"</tr></table>";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($this->mockDefault, $this->mockAlternative))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));
		$this->object->setValue($this->defaultName);

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_alternativeChecked() {
		$expectedJsVar = "js_script_param";
		$expectedName = "__{$this->defaultName}__{$this->alternativeName}__";
		$expected = "<table class=\"either_or\"><tr><td colspan=\"2\"><label for=\"{$expectedName}\">{$this->defaultName} or {$this->alternativeName}
			<a class=\"param_help\" id=\"{$expectedJsVar}\">&amp;</a><br/>" . 
			"<input type=\"radio\" name=\"{$expectedName}\" value=\"\">Neither</label></td></tr>" . 
			"<tr>" . 
			"<td><label for=\"{$expectedName}\"><input type=\"radio\" name=\"{$expectedName}\" value=\"{$this->defaultName}\">
				{$this->defaultForm}</label></td>" . 
				"<td><label for=\"{$expectedName}\"><input type=\"radio\" name=\"{$expectedName}\" value=\"{$this->alternativeName}\" checked>
				{$this->alternativeForm}</label></td>" . 
			"</tr></table>";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($this->mockDefault, $this->mockAlternative))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));
		$this->object->setValue($this->alternativeName);

		$actual = $this->object->renderForForm($disabled = false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::renderFormScript
	 */
	public function testRenderFormScript_disabled() {

		$actual = $this->object->renderFormScript("js_script", $disabled = true);

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::renderFormScript
	 */
	public function testRenderFormScript_notDisabled() {
		$expectedJsVar = "js_script___defaultName__alternativeName__";
		$parentScript = "var {$expectedJsVar} = js_script.find(\"[name=__--defaultName__--alternativeName__]\");\n";
		$expected = $parentScript . "\tmakeEitherOr({$expectedJsVar});{$expectedJsVar}.change();\n{$this->defaultScript}{$this->alternativeScript}";

		$actual = $this->object->renderFormScript("js_script", $disabled = false);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::isValueValid
	 */
	public function testIsValueValid_noValue() {

		$actual = $this->object->isValueValid(false);

		$this->assertTrue($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::isValueValid
	 */
	public function testIsValueValid_defaultValue() {

		$actual = $this->object->isValueValid($this->defaultName);

		$this->assertTrue($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::isValueValid
	 */
	public function testIsValueValid_alternativeValue() {

		$actual = $this->object->isValueValid($this->alternativeName);

		$this->assertTrue($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::isValueValid
	 */
	public function testIsValueValid_otherValue() {

		$actual = $this->object->isValueValid($this->alternativeName . "_bad");

		$this->assertFalse($actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::setValue
	 * @expectedException \Models\Scripts\ScriptException
	 */
	public function testSetValue_invalidValue() {

		$this->object->setValue($this->defaultName . "_bad");

		$this->fail("setValue should have thrown an exception");
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::setValue
	 */
	public function testSetValue_defaultValue() {
		$expectedValue = $this->defaultName;
		$expecteds = array(
			'value' => $expectedValue,
			'selection' => $this->mockDefault,
			'non_selection' => $this->mockAlternative,
		);
		$actuals = array();

		$this->object->setValue($expectedValue);
			
		$actuals['value'] = $this->object->getValue();
		$actuals['selection'] = $this->object->getSelection();
		$actuals['non_selection'] = $this->object->getNonSelection();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::setValue
	 */
	public function testSetValue_alternativeValue() {
		$expectedValue = $this->alternativeName;
		$expecteds = array(
			'value' => $expectedValue,
			'selection' => $this->mockAlternative,
			'non_selection' => $this->mockDefault,
		);
		$actuals = array();

		$this->object->setValue($expectedValue);
			
		$actuals['value'] = $this->object->getValue();
		$actuals['selection'] = $this->object->getSelection();
		$actuals['non_selection'] = $this->object->getNonSelection();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::getUnselectedValue
	 */
	public function testGetUnselectedValue_valueIsNotSet() {
		$this->object->setValue(false);
		$this->object->setNonSelection($this->mockAlternative);

		$actual = $this->object->getUnselectedValue();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::getUnselectedValue
	 */
	public function testGetUnselectedValue_nonSelectionIsNotSet() {
		$this->object->setValue($this->defaultName);
		$this->object->setNonSelection(NULL);

		$actual = $this->object->getUnselectedValue();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::getUnselectedValue
	 */
	public function testGetUnselectedValue_everythingIsSet() {
		$expected = $this->alternativeName;
		$this->object->setValue($this->defaultName);
		$this->object->setNonSelection($this->mockAlternative);

		$actual = $this->object->getUnselectedValue();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::getSelection
	 */
	public function testGetSelection() {

		$actual = $this->object->getSelection();

		$this->assertNull($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::setSelection
	 */
	public function testSetSelection() {
		$expected = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->getMock();

		$this->object->setSelection($expected);

		$actual = $this->object->getSelection();
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::getNonSelection
	 */
	public function testGetNonSelection() {

		$actual = $this->object->getNonSelection();

		$this->assertNull($actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::setNonSelection
	 */
	public function testSetNonSelection() {
		$expected = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->getMock();

		$this->object->setNonSelection($expected);

		$actual = $this->object->getNonSelection();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::acceptInput
	 */
	public function testAcceptInput_parentThrowsException() {
		$expected = "The parameter {$this->object->getName()} can only be used when:";
		$actual = "";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($this->mockDefault, $this->mockAlternative))
			->setMethods(array("getUnselectedValue"))
			->getMock();
		$this->object->expects($this->never())->method("getUnselectedValue");
		$input = array($this->object->getName() => $this->defaultName);
		$this->object->setIsExcludedByDefault(true);
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::acceptInput
	 */
	public function testAcceptInput_parentDoesNotThrowsException_parentDoesNotSetTruthyValue() {
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($this->mockDefault, $this->mockAlternative))
			->setMethods(array("getUnselectedValue"))
			->getMock();
		$this->object->expects($this->never())->method("getUnselectedValue");
		$input = array($this->object->getName() => "");

		$this->object->acceptInput($input);

	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::acceptInput
	 */
	public function testAcceptInput_parentPasses_failsBecauseSelectedValueIsNotSet() {
		$expected = "Since {$this->object->getName()} is set to {$this->defaultName}, that parameter must be specified.";
		$actual = "";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($this->mockDefault, $this->mockAlternative))
			->setMethods(array("getUnselectedValue"))
			->getMock();
		$input = array($this->object->getName() => $this->defaultName);
		$this->object->expects($this->never())->method("getUnselectedValue");
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::acceptInput
	 */
	public function testAcceptInput_parentPasses_failsBecauseSelectedValueIsSetButToEmptyString() {
		$expected = "Since {$this->object->getName()} is set to {$this->defaultName}, that parameter must be specified.";
		$actual = "";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($this->mockDefault, $this->mockAlternative))
			->setMethods(array("getUnselectedValue"))
			->getMock();
		$input = array($this->object->getName() => $this->defaultName, $this->defaultName => "");
		$this->object->expects($this->never())->method("getUnselectedValue");
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::acceptInput
	 */
	public function testAcceptInput_parentPasses_failsBecauseUnselectedValueIsSet() {
		$expected = "Since {$this->object->getName()} is set to {$this->defaultName}, {$this->alternativeName} is not allowed.";
		$actual = "";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($this->mockDefault, $this->mockAlternative))
			->setMethods(array("getUnselectedValue"))
			->getMock();
		$input = array($this->object->getName() => $this->defaultName, $this->defaultName => true, $this->alternativeName => true);
		$this->object->expects($this->exactly(3))->method("getUnselectedValue")->will($this->returnValue($this->alternativeName));
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::acceptInput
	 */
	public function testAcceptInput_parentPasses_passesBecauseUnselectedValueIsSetButEmptyString() {
		$expected = "";
		$mockDefault = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->defaultName, "defaultValue"))
			->setMethods(array("acceptInput"))
			->getMock();
		$mockDefault->expects($this->once())->method("acceptInput");
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($mockDefault, $this->mockAlternative))
			->setMethods(array("getUnselectedValue"))
			->getMock();
		$input = array($this->object->getName() => $this->defaultName, $this->defaultName => true, $this->alternativeName => "");
		$this->object->expects($this->exactly(2))->method("getUnselectedValue")->will($this->returnValue($this->alternativeName));

		$this->object->acceptInput($input);

	}
	/**
	 * @covers \Models\Scripts\Parameters\EitherOrParameter::acceptInput
	 */
	public function testAcceptInput_parentPasses_childPasses() {
		$expected = "";
		$mockDefault = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->defaultName, "defaultValue"))
			->setMethods(array("acceptInput"))
			->getMock();
		$mockDefault->expects($this->once())->method("acceptInput");
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\EitherOrParameter')
			->setConstructorArgs(array($mockDefault, $this->mockAlternative))
			->setMethods(array("getUnselectedValue"))
			->getMock();
		$input = array($this->object->getName() => $this->defaultName, $this->defaultName => true);
		$this->object->expects($this->once())->method("getUnselectedValue")->will($this->returnValue($this->alternativeName));

		$this->object->acceptInput($input);

	}
}
