<?php

namespace Models\Scripts\Parameters;

class VersionParameterTest extends \PHPUnit_Framework_TestCase {

	private $object;
	private $mockScript = NULL;

	public static function setUpBeforeClass() {
		error_log("VersionParameterTest");
	}

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$stubGetter = new \Stubs\StubGetter();
		$this->mockScript = $stubGetter->getScript();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue("js_script"));
		$this->mockScript->expects($this->any())->method("getHtmlId")->will($this->returnValue("script"));
	}

	public function setUp() {
		$this->object = new VersionParameter($this->mockScript);
	}

	public function testRenderForOperatingSystem() {
		$this->assertEmpty($this->object->renderForOperatingSystem());
	}

	public function testRenderForForm() {
		$this->assertEquals("<a class=\"button\" onclick=\"alert('mock version 1.7');\">Version</a>", $this->object->renderForForm($disabled = false, $this->mockScript));
	}
}
