<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

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

		$this->object = new \Models\Scripts\Parameters\Label($this->value);

		$actual = $this->object->getValue();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\Label::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {
		$expected = "";

		$actual = $this->object->renderForOperatingSystem();

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\Label::renderForForm
	 */
	public function testRenderForForm_disabled() {
		$expected = "<p><strong>{$this->value}</strong></p>\n";

		$actual = $this->object->renderForForm(true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\Label::renderForForm
	 */
	public function testRenderForForm_notDisabled() {
		$expected = "<p><strong>{$this->value}</strong></p>\n";

		$actual = $this->object->renderForForm(false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\Label::acceptInput
	 */
	public function testAcceptInput_inputEmpty() {
		$expected = NULL;

		$actual = $this->object->acceptInput(array());

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\Label::acceptInput
	 */
	public function testAcceptInput_inputNotEmpty() {
		$expected = NULL;

		$actual = $this->object->acceptInput(array(1, 2, 3));

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\Label::renderFormScript
	 */
	public function testRenderFormScript_disabled() {
		$expected = "";

		$actual = $this->object->renderFormScript("formJsVar", true);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\Label::renderFormScript
	 */
	public function testRenderFormScript_notDisabled() {
		$expected = "";

		$actual = $this->object->renderFormScript("formJsVar", false);

		$this->assertEquals($expected, $actual);
	}
}
