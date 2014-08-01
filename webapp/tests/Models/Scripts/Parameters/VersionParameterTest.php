<?php

namespace Models\Scripts\Parameters;

class VersionParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("VersionParameterTest");
	}

	private $mockScript = NULL;
	private $object;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("getHtmlId"))
			->getMockForAbstractClass();
		$this->mockScript->expects($this->any())->method("getHtmlId")->will($this->returnValue("script"));
	}

	public function setUp() {
		$this->object = new VersionParameter($this->mockScript);
	}

	/**
	 * @covers \Models\Scripts\Parameters\VersionParameter::__constructor
	 */
	public function testConstructor() {
		$expecteds = array(
			'name' => "--version",
			'version_string' => "File: public/versions/script.txt"
		);
		$actuals = array();


		$actuals['name'] = $this->object->getName();
		$actuals['version_string'] = $this->object->getVersionString();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\Scripts\Parameters\VersionParameter::renderForOperatingSystem
	 */
	public function testRenderForOperatingSystem() {

		$actual = $this->object->renderForOperatingSystem();

		$this->assertEmpty($actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\VersionParameter::renderForForm
	 */
	public function testRenderForForm() {
		$expected = "<a class=\"button\" onclick=\"alert('File: public/versions/script.txt');\">Version</a>";

		$actual = $this->object->renderForForm($disabled = false, $this->mockScript);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\Parameters\VersionParameter::getVersionString
	 */
	public function testGetVersionString() {
		$expected = "File: public/versions/script.txt";

		$actual = $this->object->getVersionString();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\Scripts\Parameters\VersionParameter::setVersionString
	 */
	public function testSetVersionString() {
		$expected = "new version";

		$this->object->setVersionString($expected);

		$actual = $this->object->getVersionString();
		$this->assertEquals($expected, $actual);
	}
}
