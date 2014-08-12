<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\Parameters;

class TrueFalseParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("TrueFalseParameterTest");
	}

	private $jsVar = "js_script";
	private $mockScript = NULL;
	private $name = "--name";
	private $object;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getJsVar"))
			->getMockForAbstractClass();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue($this->jsVar));
	}

	public function setUp() {
		$this->object = new TrueFalseParameter($this->name);
	}

	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseParameter::__construct
	 */
	public function testConstructor() {
		$expecteds = array(
			"name" => $this->name,
			"value" => false,
		);
		$actuals = array();

		$this->object = new TrueFalseParameter($this->name);

		$actuals['name'] = $this->object->getName();
		$actuals['value'] = $this->object->getValue();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_valueTrue() {
		$expected = $this->name;
		$this->object->setValue(true);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem_valueFalse() {
		$expected = "";
		$this->object->setValue(false);

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseParameter::renderForForm
	 */
	public function testRenderForForm_disabled_valueTrue() {
		$expected = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\" checked disabled/> {$this->name}
			<a class=\"param_help\" id=\"{$this->jsVar}\">&amp;</a></label>";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\TrueFalseParameter')
			->setConstructorArgs(array($this->name))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnArgument(0));
		$this->object->setValue(true);

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseParameter::renderForForm
	 */
	public function testRenderForForm_disabled_valueFalse() {
		$expected = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\" disabled/> {$this->name}
			<a class=\"param_help\" id=\"{$this->jsVar}\">&amp;</a></label>";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\TrueFalseParameter')
			->setConstructorArgs(array($this->name))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnArgument(0));
		$this->object->setValue(false);

		$actual = $this->object->renderForForm($disabled = true, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_valueTrue() {
		$expected = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\" checked/> {$this->name}
			<a class=\"param_help\" id=\"{$this->jsVar}\">&amp;</a></label>";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\TrueFalseParameter')
			->setConstructorArgs(array($this->name))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnArgument(0));
		$this->object->setValue(true);

		$actual = $this->object->renderForForm($disabled = false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\TrueFalseParameter::renderForForm
	 */
	public function testRenderForForm_notDisabled_valueFalse() {
		$expected = "<label for=\"{$this->name}\"><input type=\"checkbox\" name=\"{$this->name}\"/> {$this->name}
			<a class=\"param_help\" id=\"{$this->jsVar}\">&amp;</a></label>";
		$this->object = $this->getMockBuilder('\Models\Scripts\Parameters\TrueFalseParameter')
			->setConstructorArgs(array($this->name))
			->setMethods(array("getJsVar"))
			->getMock();
		$this->object->expects($this->once())->method("getJsVar")->will($this->returnArgument(0));
		$this->object->setValue(false);

		$actual = $this->object->renderForForm($disabled = false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
}
