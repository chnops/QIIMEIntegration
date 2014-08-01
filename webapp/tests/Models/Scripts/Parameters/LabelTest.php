<?php

namespace Models\Scripts\Parameter;

class LabelTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("LabelTest");
	}

	private $value = "value";
	private $mockScript = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}
	public function setUp() {
		$this->object = new \Models\Scripts\Parameters\Label($this->value);
	}

	/**
	 * @covers \Models\Scripts\Parameters\Label::__construct
	 */
	public function testConstructor() {
		$expected = $this->value;


		$actual = $this->object->getValue();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\Label::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEmpty($actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\Label::renderForForm
	 */
	public function testRenderForForm() {
		$expecteds = array(
			"disabled" => "<p><strong>{$this->value}</strong></p>\n",
			"not_disabled" => "<p><strong>{$this->value}</strong></p>\n",
		);
		$actuals = array();

		$actuals['disabled'] = $this->object->renderForForm(true, $this->mockScript);
		$actuals['not_disabled'] = $this->object->renderForForm(false, $this->mockScript);

		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\Label::acceptInput
	 */
	public function testAcceptInput() {
		$expecteds = array(
			"empty" => NULL,
			"not_empty" => NULL,
		);
		$actuals = array();

		$actuals['empty'] = $this->object->acceptInput(array());
		$actuals['not_empty'] = $this->object->acceptInput(array(1, 2, 3));

		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\Label::renderFormScript
	 */
	public function testRenderFormScript() {
		$expecteds = array(
			"disabled" => "",
			"not_disabled" => "",
		);
		$actuals = array();

		$actuals['disabled'] = $this->object->renderFormScript("formJsVar", true);
		$actuals['not_disabled'] = $this->object->renderFormScript("formJsVar", false);

		$this->assertEquals($expecteds, $actuals);
	}
}
