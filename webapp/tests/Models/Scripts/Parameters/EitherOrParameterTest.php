<?php

namespace Models\Scripts\Parameter;

class EitherOrParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("EitherOrParameterTest");
	}

	private $mockParam = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockParam = $this->getMockBuilder('\Models\Scripts\Parameters\DefaultParameter')
			->getMock();
	}
	public function setUp() {
		$this->object = new \Models\Scripts\Parameters\EitherOrParameter($this->mockParam, $this->mockParam);
	}

	/**
	 * @covers \Models\Scripts\QIIME\
	 */
	public function testIncludes() {
		$this->assertTrue(false);
	}
}
