<?php

namespace Models\Scripts\Parameters;
use Models\Scripts\ScriptException;

class DefaultParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("DefaultParameterTest");
	}

	private $name = "--name";
	private $value = "value";
	private $scriptJsVar = "js_script";
	private $mockScript = NULL;
	private $paramHelp = "<a class=\"param_help\" id=\"js_script_name\">&amp;</a>";
	private $otherName = "--other_name";
	private $otherValue = "other_value";
	private $mockParameter = NULL;
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
		$this->mockParameter = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->otherName, $this->otherValue))
			->setMethods(NULL)
			->getMock();
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

		$this->object = new DefaultParameter($this->name, $this->value);

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
	 */
	public function testSetValue_inValidValue() {
		$expected = new ScriptException("An invalid value was provided for the parameter: ");
		$actual = NULL;
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("isValueValid"))
			->getMock();
		$this->object->expects($this->once())->method("isValueValid")->will($this->returnValue(false));
		try {

			$this->object->setValue("anyValue");

		}
		catch(ScriptException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
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
		$expected = true;
		
		$actual = $this->object->isValueValid("anyValueAtAll");

		$this->assertSame($expected, $actual);
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
		$expectedTwoLengthName = "-n";
		$expected = $expectedTwoLengthName . " '" . $this->value . "'";
		$this->object->setName($expectedTwoLengthName);

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
		$expectedEscapableValue = "val'ue";
		$expected = $this->name . "='val'\''ue'";
		$this->object->setValue($expectedEscapableValue);

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
	public function testRenderFormScript_disabled() {
		$expected = "";

		$actual = $this->object->renderFormScript($this->mockScript->getJsVar(), $disabled = true);

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderFormScript
	 */
	public function testRenderFormScript_isAlwaysRequired_isATrigger_isNotADependent() {
		$expectedJsVar = $this->mockScript->getJsVar() . "_parameter";
		$expected = "var {$expectedJsVar} = {$this->mockScript->getJsVar()}.find(\"[name={$this->name}]\");" .
			"requireParam({$expectedJsVar});" .
			"makeTrigger({$expectedJsVar});" .
			"\n";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));
		$this->object->setIsAlwaysRequired(true);
		$this->object->isATrigger(true);
		$this->object->setRequiringTriggers(array());
		$this->object->setAllowingTriggers(array());
		$this->object->setExcludingTriggers(array());

		$actual = $this->object->renderFormScript($this->mockScript->getJsVar(), $disabled = false);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderFormScript
	 */
	public function testRenderFormScript_isNotAlwaysRequired_isNotATrigger_isADependent_hasAllowingTriggers_hasRequiringTriggers_hasExcludingTriggers_allOneTrigger() {
		$expectedJsVar = $this->mockScript->getJsVar() . "_parameter";
		$expectedTriggerVar = $this->mockScript->getJsVar() . "_other_name";
		$expectedRequiringValue = true;
		$expectedAllowingValue = false;
		$expectedExcludingValue = "value";
		$expected = "var {$expectedJsVar} = {$this->mockScript->getJsVar()}.find(\"[name={$this->name}]\");" .
			"makeDependent({$expectedJsVar});" .
			"{$expectedJsVar}.listenTo({$expectedTriggerVar});" .
			"{$expectedJsVar}.allowOn('{$this->otherName}', false);" . 
			"{$expectedJsVar}.requireOn('{$this->otherName}', true);" . 
			"{$expectedJsVar}.excludeOn('{$this->otherName}', '{$expectedExcludingValue}');" . 
			"\n";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));
		$this->object->setIsAlwaysRequired(false);
		$this->object->isATrigger(false);
		$this->object->setRequiringTriggers(array(array("parameter" => $this->mockParameter, "value" => $expectedRequiringValue)));
		$this->object->setAllowingTriggers(array(array("parameter" => $this->mockParameter, "value" => $expectedAllowingValue)));
		$this->object->setExcludingTriggers(array(array("parameter" => $this->mockParameter, "value" => $expectedExcludingValue)));

		$actual = $this->object->renderFormScript($this->mockScript->getJsVar(), $disabled = false);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderFormScript
	 */
	public function testRenderFormScript_isNotAlwaysRequired_isNotATrigger_isADependent_hasAllowingTriggers_hasNoRequiringTriggers_hasNoExcludingTriggers_onlyOneTrigger() {
		$expectedJsVar = $this->mockScript->getJsVar() . "_parameter";
		$expectedTriggerVar = $this->mockScript->getJsVar() . "_other_name";
		$expectedRequiringValue = true;
		$expectedAllowingValue = false;
		$expectedExcludingValue = "value";
		$expected = "var {$expectedJsVar} = {$this->mockScript->getJsVar()}.find(\"[name={$this->name}]\");" .
			"makeDependent({$expectedJsVar});" .
			"{$expectedJsVar}.listenTo({$expectedTriggerVar});" .
			"{$expectedJsVar}.allowOn('{$this->otherName}', false);" . 
			"\n";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));
		$this->object->setIsAlwaysRequired(false);
		$this->object->isATrigger(false);
		$this->object->setRequiringTriggers(array());
		$this->object->setAllowingTriggers(array(array("parameter" => $this->mockParameter, "value" => $expectedAllowingValue)));
		$this->object->setExcludingTriggers(array());

		$actual = $this->object->renderFormScript($this->mockScript->getJsVar(), $disabled = false);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::renderFormScript
	 */
	public function testRenderFormScript_isNotAlwaysRequired_isNotATrigger_isADependent_hasNoAllowingTriggers_hasRequiringTriggers_hasExcludingTriggers_twoDifferentTrigger() {
		$expectedJsVar = $this->mockScript->getJsVar() . "_parameter";
		$expectedRequiringTriggerVar = $this->mockScript->getJsVar() . "_requiring_name";
		$expectedAllowingTriggerVar = $this->mockScript->getJsVar() . "_allowing_name";
		$expectedExcludingTriggerVar = $this->mockScript->getJsVar() . "_excluding_name";
		$expectedRequiringValue = true;
		$expectedAllowingValue = false;
		$expectedExcludingValue = "value";
		$expected = "var {$expectedJsVar} = {$this->mockScript->getJsVar()}.find(\"[name={$this->name}]\");" .
			"makeDependent({$expectedJsVar});" .
			"{$expectedJsVar}.listenTo({$expectedAllowingTriggerVar});" .
			"{$expectedJsVar}.listenTo({$expectedRequiringTriggerVar});" .
			"{$expectedJsVar}.listenTo({$expectedExcludingTriggerVar});" .
			"{$expectedJsVar}.allowOn('allower', false);" . 
			"{$expectedJsVar}.requireOn('requirer', true);" . 
			"{$expectedJsVar}.excludeOn('excluder', '{$expectedExcludingValue}');" . 
			"\n";
		$mockRequiringTrigger = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("getJsVar", "getName"))
			->getMock();
		$mockAllowingTrigger = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("getJsVar", "getName"))
			->getMock();
		$mockExcludingTrigger = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->disableOriginalConstructor()
			->setMethods(array("getJsVar", "getName"))
			->getMock();
		$mockRequiringTrigger->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedRequiringTriggerVar));
		$mockRequiringTrigger->expects($this->once())->method("getName")->will($this->returnValue("requirer"));
		$mockAllowingTrigger->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedAllowingTriggerVar));
		$mockAllowingTrigger->expects($this->once())->method("getName")->will($this->returnValue("allower"));
		$mockExcludingTrigger->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedExcludingTriggerVar));
		$mockExcludingTrigger->expects($this->once())->method("getName")->will($this->returnValue("excluder"));
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnValue($expectedJsVar));
		$this->object->setIsAlwaysRequired(false);
		$this->object->isATrigger(false);
		$this->object->setRequiringTriggers(array(array("parameter" => $mockRequiringTrigger, "value" => $expectedRequiringValue)));
		$this->object->setAllowingTriggers(array(array("parameter" => $mockAllowingTrigger, "value" => $expectedAllowingValue)));
		$this->object->setExcludingTriggers(array(array("parameter" => $mockExcludingTrigger, "value" => $expectedExcludingValue)));

		$actual = $this->object->renderFormScript($this->mockScript->getJsVar(), $disabled = false);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput_paramNotPresent_isAlwaysRequired() {
		$expecteds = array(
			'object_value' => false,
			'error_message' => "The parameter {$this->name} is required",
		);
		$actuals = array();
		$dismissers = array();
		$requirers = array();
		$input = array();
		$this->object->setIsAlwaysRequired(true);
		try {
		
			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actuals['error_message'] = $ex->getMessage();
		}
		$actuals['object_value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput_paramPresentButFalsy_isAlwaysRequired() {
		$expecteds = array(
			'object_value' => false,
			'error_message' => "The parameter {$this->name} is required",
		);
		$actuals = array();
		$dismissers = array();
		$requirers = array();
		$input = array($this->name => "");
		$this->object->setIsAlwaysRequired(true);
		try {
		
			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actuals['error_message'] = $ex->getMessage();
		}
		$actuals['object_value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput_paramNotPresent_activeDismissers_activeRequirers() {
		$expecteds = array(
			'object_value' => false,
		);
		$actuals = array();
		$dismissers = array(1, 2, 3);
		$requirers = array(1, 2, 3);
		$input = array();
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getActiveTriggers"))
			->getMock();
		$this->object->expects($this->once())->method("getActiveTriggers")->will($this->returnArgument(0));
		$this->object->setIsAlwaysRequired(false);
		$this->object->setDismissingTriggers($dismissers);
		$this->object->setRequiringTriggers($requirers);
		
		$this->object->acceptInput($input);

		$actuals['object_value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput_paramNotPresent_zeroActiveDismissers_activeRequirers() {
		$expecteds = array(
			'object_value' => false,
			'error_message' => "The parameter {$this->name} is required when:<br/>&nbsp;- {$this->otherName} is set to {$this->otherValue}",
		);
		$actuals = array();
		$dismissers = array();
		$requirers = array(
			array("parameter" => $this->mockParameter, "value" => $this->otherValue),
		);
		$input = array();
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getActiveTriggers"))
			->getMock();
		$this->object->expects($this->exactly(2))->method("getActiveTriggers")->will($this->returnArgument(0));
		$this->object->setIsAlwaysRequired(false);
		$this->object->setDismissingTriggers($dismissers);
		$this->object->setRequiringTriggers($requirers);
		try {
		
			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actuals['error_message'] = $ex->getMessage();
		}
		$actuals['object_value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput_paramNotPresent_zeroActiveDismissers_zeroActiveRequirers() {
		$expecteds = array(
			'object_value' => false,
		);
		$actuals = array();
		$dismissers = array();
		$requirers = array();
		$input = array();
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getActiveTriggers"))
			->getMock();
		$this->object->expects($this->exactly(2))->method("getActiveTriggers")->will($this->returnArgument(0));
		$this->object->setIsAlwaysRequired(false);
		$this->object->setDismissingTriggers($dismissers);
		$this->object->setRequiringTriggers($requirers);
		
		$this->object->acceptInput($input);

		$actuals['object_value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput_paramPresent_manyActiveExcluders() {
		$expecteds = array(
			"object_value" => $this->otherValue,
			"error_message" => "The parameter {$this->name} cannot be used when:<br/>&nbsp;- {$this->otherName} is set",
		);
		$actuals = array();
		$excluders = array(array("parameter" => $this->mockParameter, "value" => true));
		$allowers = array();
		$input = array($this->object->getName() => $this->otherValue);
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getActiveTriggers", "isExcludedByDefault"))
			->getMock();
		$this->object->expects($this->once())->method("getActiveTriggers")->will($this->returnArgument(0));
		$this->object->expects($this->never())->method("isExcludedByDefault")->will($this->returnValue(true));
		$this->object->setExcludingTriggers($excluders);
		$this->object->setAllowingTriggers($allowers);
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch(ScriptException $ex) {
			$actuals['error_message'] = $ex->getMessage();
		}
		$actuals['object_value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput_paramPresent_zeroActiveExcluders_notExcludedByDefault() {
		$expecteds = array(
			"object_value" => $this->otherValue,
		);
		$actuals = array();
		$excluders = array();
		$allowers = array();
		$input = array($this->object->getName() => $this->otherValue);
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getActiveTriggers", "isExcludedByDefault"))
			->getMock();
		$this->object->expects($this->once())->method("getActiveTriggers")->will($this->returnArgument(0));
		$this->object->expects($this->once())->method("isExcludedByDefault")->will($this->returnValue(false));
		$this->object->setExcludingTriggers($excluders);
		$this->object->setAllowingTriggers($allowers);
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInput should not have thrown an exception: {$ex->getMessage()}");
		}
		$actuals['object_value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput_paramPresent_zeroActiveExcluders_excludedByDefault_zeroActiveAllowers() {
		$expecteds = array(
			"object_value" => $this->otherValue,
			"error_message" => "The parameter {$this->name} can only be used when:<br/>&nbsp;- --other_name is set",
		);
		$actuals = array();
		$excluders = array();
		$allowers = array(array("parameter" => $this->mockParameter, "value" => true));
		$input = array($this->object->getName() => $this->otherValue);
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getActiveTriggers", "isExcludedByDefault"))
			->getMock();
		$this->object->expects($this->exactly(2))->method("getActiveTriggers")->will($this->returnValue(array()));
		$this->object->expects($this->once())->method("isExcludedByDefault")->will($this->returnValue(true));
		$this->object->setExcludingTriggers($excluders);
		$this->object->setAllowingTriggers($allowers);
		try {

			$this->object->acceptInput($input);

			$this->fail("acceptInput should have thrown an exception");
		}
		catch(ScriptException $ex) {
			$actuals['error_message'] = $ex->getMessage();
		}
		$actuals['object_value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::acceptInput
	 */
	public function testAcceptInput_paramPresent_zeroActiveExcluders_excludedByDefault_manyActiveAllowers() {
		$expecteds = array(
			"object_value" => $this->otherValue,
		);
		$actuals = array();
		$excluders = array();
		$allowers = array(array("parameter" => $this->mockParameter, "value" => true));
		$input = array($this->object->getName() => $this->otherValue);
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->setConstructorArgs(array($this->name, $this->value))
			->setMethods(array("getActiveTriggers", "isExcludedByDefault"))
			->getMock();
		$this->object->expects($this->exactly(2))->method("getActiveTriggers")->will($this->returnArgument(0));
		$this->object->expects($this->once())->method("isExcludedByDefault")->will($this->returnValue(true));
		$this->object->setExcludingTriggers($excluders);
		$this->object->setAllowingTriggers($allowers);
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$this->fail("acceptInput should not have thrown an exception: {$ex->getMessage()}");
		}
		$actuals['object_value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::convertTriggerToWhenClause
	 */
	public function testConvertTriggerToWhenClause_valueFalse() {
		$expected = "<br/>&nbsp;- --other_name is not set";
		$trigger = array("parameter" => $this->mockParameter, "value" => false);

		$actual = $this->object->convertTriggerToWhenClause($trigger);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::convertTriggerToWhenClause
	 */
	public function testConvertTriggerToWhenClause_valueTrue() {
		$expected = "<br/>&nbsp;- --other_name is set";
		$trigger = array("parameter" => $this->mockParameter, "value" => true);

		$actual = $this->object->convertTriggerToWhenClause($trigger);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::convertTriggerToWhenClause
	 */
	public function testConvertTriggerToWhenClause_valueOtherTruthy() {
		$expectedValue = $this->otherValue;
		$expected = "<br/>&nbsp;- --other_name is set to {$expectedValue}";
		$trigger = array("parameter" => $this->mockParameter, "value" => $expectedValue);

		$actual = $this->object->convertTriggerToWhenClause($trigger);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::convertTriggerToWhenClause
	 */
	public function testConvertTriggerToWhenClause_valueOtherFalsy() {
		$expectedValue = 0;
		$expected = "<br/>&nbsp;- --other_name is set to {$expectedValue}";
		$trigger = array("parameter" => $this->mockParameter, "value" => $expectedValue);

		$actual = $this->object->convertTriggerToWhenClause($trigger);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_triggersEmpty() {
		$expected = array();
		$triggers = array();
		$input = array();

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueFalse_paramIsNotSet() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$triggers = array(array("parameter" => $expectedParam, "value" => false));
		$expected = $triggers;
		$input = array();

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueFalse_paramIsEmptyString() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array(array("parameter" => $expectedParam, "value" => false));
		$triggers = array(array("parameter" => $expectedParam, "value" => false));
		$input = array($this->name => "");

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueFalse_paramIs0() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => false));
		$input = array($this->name => "0");

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueFalse_paramIsTruthy() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => false));
		$input = array($this->name => $this->value);

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueTrue_paramIsNotSet() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => true));
		$input = array();

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueTrue_paramIsEmptyString() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => true));
		$input = array($this->name => "");

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueTrue_paramIs0() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array(array("parameter" => $expectedParam, "value" => true)); 
		$triggers = array(array("parameter" => $expectedParam, "value" => true));
		$input = array($this->name => "0");

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueTrue_paramIsTruthy() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array(array("parameter" => $expectedParam, "value" => true));
		$triggers = array(array("parameter" => $expectedParam, "value" => true));
		$input = array($this->name => $this->value);

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueFalsey_paramIsNotSet() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => 0));
		$input = array();

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueFalsey_paramIsEmptyString() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => 0));
		$input = array($this->name => "");

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueFalsey_paramIs0() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array(array("parameter" => $expectedParam, "value" => "0")); 
		$triggers = array(array("parameter" => $expectedParam, "value" => "0"));
		$input = array($this->name => "0");

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueFalsey_paramIsTruthy() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => 0));
		$input = array($this->name => $this->value);

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueTruthy_paramIsNotSet() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => $this->value));
		$input = array();

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueTruthy_paramIsEmptyString() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => $this->value));
		$input = array($this->name => "");

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueTruthy_paramIs0() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => $this->value));
		$input = array($this->name => "0");

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueTruthy_paramIsCorrectTruthy() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array(array("parameter" => $expectedParam, "value" => $this->value)); 
		$triggers = array(array("parameter" => $expectedParam, "value" => $this->value));
		$input = array($this->name => $this->value);

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getActiveTriggers
	 */
	public function testGetActiveTriggers_valueTruthy_paramIsIncorrectTruthy() {
		$expectedParam = new DefaultParameter($this->name, $this->value);
		$expected = array();
		$triggers = array(array("parameter" => $expectedParam, "value" => $this->value));
		$input = array($this->name => "not_" . $this->value);

		$actual = $this->object->getActiveTriggers($triggers, $input);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::requireIf
	 */
	public function testRequireIf_noTrigger_noValue() {
		$expecteds = array(
			"is_always_required" => true,
			"requiring_triggers" => array(),
		);
		$actuals = array();

		$this->object->requireIf();

		$actuals['is_always_required'] = $this->object->isAlwaysRequired();
		$actuals['requiring_triggers'] = $this->object->getRequiringTriggers();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::requireIf
	 */
	public function testRequireIf_trigger_noValue() {
		$expecteds = array(
			"is_always_required" => false,
			"requiring_triggers" => array(
				array("parameter" => $this->mockParameter, "value" => true),
			),
			"is_a_trigger" => true,
		);
		$actuals = array();

		$this->object->requireIf($this->mockParameter);

		$actuals['is_always_required'] = $this->object->isAlwaysRequired();
		$actuals['requiring_triggers'] = $this->object->getRequiringTriggers();
		$actuals['is_a_trigger'] = $this->mockParameter->isATrigger();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::requireIf
	 */
	public function testRequireIf_trigger_value() {
		$expectedValue = "value";
		$expecteds = array(
			"is_always_required" => false,
			"requiring_triggers" => array(
				array("parameter" => $this->mockParameter, "value" => $expectedValue),
			),
			"is_a_trigger" => true,
		);
		$actuals = array();

		$this->object->requireIf($this->mockParameter, $expectedValue);

		$actuals['is_always_required'] = $this->object->isAlwaysRequired();
		$actuals['requiring_triggers'] = $this->object->getRequiringTriggers();
		$actuals['is_a_trigger'] = $this->mockParameter->isATrigger();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::dismissIf
	 */
	public function testDismissIf_noValue() {
		$expecteds = array(
			"dismissing_triggers" => array(
				array("parameter" => $this->mockParameter, "value" => true),
			),
			"is_a_trigger" => true,
		);
		$actuals = array();

		$this->object->dismissIf($this->mockParameter);

		$actuals['dismissing_triggers'] = $this->object->getDismissingTriggers();
		$actuals['is_a_trigger'] = $this->mockParameter->isATrigger();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::dismissIf
	 */
	public function testDismissIf_value() {
		$expectedValue = "value";
		$expecteds = array(
			"dismissing_triggers" => array(
				array("parameter" => $this->mockParameter, "value" => $expectedValue),
			),
			"is_a_trigger" => true,
		);
		$actuals = array();

		$this->object->dismissIf($this->mockParameter, $expectedValue);

		$actuals['dismissing_triggers'] = $this->object->getDismissingTriggers();
		$actuals['is_a_trigger'] = $this->mockParameter->isATrigger();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::excludeButAllowIf
	 */
	public function testExcludeButAllowIf_noTrigger_noValue() {
		$expecteds = array(
			"is_excluded_by_default" => true,
			"allowing_triggers" => array(),
		);
		$actuals = array();

		$this->object->excludeButAllowIf();

		$actuals['is_excluded_by_default'] = $this->object->isExcludedByDefault();
		$actuals['allowing_triggers'] = $this->object->getAllowingTriggers();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::excludeButAllowIf
	 */
	public function testExcludeButAllowIf_trigger_noValue() {
		$expecteds = array(
			"is_excluded_by_default" => true,
			"allowing_triggers" => array(
				array("parameter" => $this->mockParameter, "value" => true),
			),
			"is_a_trigger" => true,
		);
		$actuals = array();

		$this->object->excludeButAllowIf($this->mockParameter);

		$actuals['is_excluded_by_default'] = $this->object->isExcludedByDefault();
		$actuals['allowing_triggers'] = $this->object->getAllowingTriggers();
		$actuals['is_a_trigger'] = $this->mockParameter->isATrigger();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::excludeButAllowIf
	 */
	public function testExcludeButAllowIf_trigger_value() {
		$expectedValue = "value";
		$expecteds = array(
			"is_excluded_by_default" => true,
			"allowing_triggers" => array(
				array("parameter" => $this->mockParameter, "value" => $expectedValue),
			),
			"is_a_trigger" => true,
		);
		$actuals = array();

		$this->object->excludeButAllowIf($this->mockParameter, $expectedValue);

		$actuals['is_excluded_by_default'] = $this->object->isExcludedByDefault();
		$actuals['allowing_triggers'] = $this->object->getAllowingTriggers();
		$actuals['is_a_trigger'] = $this->mockParameter->isATrigger();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::excludeIf
	 */
	public function testExcludeIf_noValue() {
		$expecteds = array(
			"is_excluded_by_default" => false,
			"excluding_triggers" => array(
				array("parameter" => $this->mockParameter, "value" => true),
			),
			"is_trigger" => true,
		);
		$actuals = array();

		$this->object->excludeIf($this->mockParameter);

		$actuals['is_excluded_by_default'] = $this->object->isExcludedByDefault();
		$actuals['excluding_triggers'] = $this->object->getExcludingTriggers();
		$actuals['is_trigger'] = $this->mockParameter->isATrigger();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::excludeIf
	 */
	public function testExcludeIf_value() {
		$expectedValue = "value";
		$expecteds = array(
			"is_excluded_by_default" => false,
			"excluding_triggers" => array(
				array("parameter" => $this->mockParameter, "value" => $expectedValue),
			),
			"is_trigger" => true,
		);
		$actuals = array();

		$this->object->excludeIf($this->mockParameter, $expectedValue);

		$actuals['is_excluded_by_default'] = $this->object->isExcludedByDefault();
		$actuals['excluding_triggers'] = $this->object->getExcludingTriggers();
		$actuals['is_trigger'] = $this->mockParameter->isATrigger();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::linkTo
	 */
	public function testLinkTo() {
		$expectedDisplayName = "displayName";
		$expected = new EitherOrParameter($this->object, $this->mockParameter, $expectedDisplayName);

		$actual = $this->object->linkTo($this->mockParameter, $expectedDisplayName);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::isATrigger
	 */
	public function testIsATrigger_return() {
		$expected = false;

		$actual = $this->object->isATrigger();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::isATrigger
	 */
	public function testIsATrigger_setToFalse() {
		$expected = false;

		$this->object->isATrigger($expected);

		$actual = $this->object->isATrigger();
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::isATrigger
	 */
	public function testIsATrigger_setToTrue() {
		$expected = true;

		$this->object->isATrigger($expected);

		$actual = $this->object->isATrigger();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::isAlwaysRequired
	 */
	public function testIsAlwaysRequired() {
		$expected = false;

		$actual = $this->object->isAlwaysRequired();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setIsAlwaysRequired
	 */
	public function testSetIsAlwaysRequired_true() {
		$expected = true;

		$this->object->setIsAlwaysRequired(1);

		$actual = $this->object->isAlwaysRequired();
		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setIsAlwaysRequired
	 */
	public function testSetIsAlwaysRequired_false() {
		$expected = false;

		$this->object->setIsAlwaysRequired(0);

		$actual = $this->object->isAlwaysRequired();
		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getRequiringTriggers
	 */
	public function testGetRequiringTriggers() {
		$expected = array();

		$actual = $this->object->getRequiringTriggers();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setRequiringTriggers
	 */
	public function testSetRequiringTriggers() {
		$expected = array(1,2,3);

		$this->object->setRequiringTriggers($expected);

		$actual = $this->object->getRequiringTriggers();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getDismissingTriggers
	 */
	public function testGetDismissingTriggers() {
		$expected = array();

		$actual = $this->object->getDismissingTriggers();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setDismissingTriggers
	 */
	public function testSetDismissingTriggers() {
		$expected = array(1,2,3);

		$this->object->setDismissingTriggers($expected);

		$actual = $this->object->getDismissingTriggers();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::isExcludedByDefault
	 */
	public function testIsExcludedByDefault() {
		$expected = false;

		$actual = $this->object->isExcludedByDefault();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setIsExcludedByDefault
	 */
	public function testSetIsExcludedByDefault_true() {
		$expected = true;

		$this->object->setIsExcludedByDefault(1);

		$actual = $this->object->isExcludedByDefault();
		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setIsExcludedByDefault
	 */
	public function testSetIsExcludedByDefault_false() {
		$expected = false;

		$this->object->setIsExcludedByDefault(0);

		$actual = $this->object->isExcludedByDefault();
		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getAllowingTriggers
	 */
	public function testGetAllowingTriggers() {
		$expected = array();

		$actual = $this->object->getAllowingTriggers();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setAllowingTriggers
	 */
	public function testSetAllowingTriggers() {
		$expected = array(1,2,3);

		$this->object->setAllowingTriggers($expected);

		$actual = $this->object->getAllowingTriggers();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::getExcludingTriggers
	 */
	public function testGetExcludingTriggers() {
		$expected = array();

		$actual = $this->object->getExcludingTriggers();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\DefaultParameter::setExcludingTriggers
	 */
	public function testSetExcludingTriggers() {
		$expected = array(1,2,3);

		$this->object->setExcludingTriggers($expected);

		$actual = $this->object->getExcludingTriggers();
		$this->assertEquals($expected, $actual);
	}
}
