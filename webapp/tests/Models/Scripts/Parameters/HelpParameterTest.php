<?php

namespace Models\Scripts\Parameters;

class HelpParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("HelpParameterTest");
	}

	private $mockScript = NULL; 
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getHtmlId"))
			->getMockForAbstractClass();
		$this->mockScript->expects($this->any())->method("getHtmlId")->will($this->returnValue("script"));
	}

	public function setUp() {
		$this->object = new HelpParameter();
	}

	/**
	 * @covers \Models\Scripts\Parameters\HelpParameter::__construct
	 */
	public function testConstructor() {
		$expected = "--help";

		$this->object = new HelpParameter();

		$actual = $this->object->getName();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\HelpParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expected = "<a href=\"public/manual/{$this->mockScript->getHtmlId()}.txt\" target=\"_blank\" class=\"button\">See manual page</a>";

		$actual = $this->object->renderForForm($disabled = false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\HelpParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {
		$expected = "";

		$actual = $this->object->renderForOperatingSystem();

		$this->assertSame($expected, $actual);
	}
}
