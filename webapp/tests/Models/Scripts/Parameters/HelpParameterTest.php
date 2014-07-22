<?php

namespace Models\Scripts\Parameters;

class HelpParameterTest extends \PHPUnit_Framework_TestCase {

	private $mockScript; 

	private $object;

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$stubGetter = new \Stubs\StubGetter();
		$this->mockScript = $stubGetter->getScript();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue("js_script"));
	}

	public static function setUpBeforeClass() {
		error_log("HelpParameterTest");
	}

	public function setUp() {
		$mockScript = $this->getMock("\\Models\\Scripts\\ScriptI");
		$mockScript->expects($this->any())->method("getHtmlId")->will($this->returnValue("mock"));
		$this->object = new HelpParameter($mockScript);
	}

	public function testAll() {
		$this->assertEmpty($this->object->getName());
		$this->assertEmpty($this->object->renderForOperatingSystem());
		$expectedLink = "<a href=\"public/manual/mock.txt\" target=\"_blank\" class=\"button\">See manual page</a>";
		$this->assertEquals($expectedLink, $this->object->renderForForm($disabled = false, $this->mockScript));
	}

}
