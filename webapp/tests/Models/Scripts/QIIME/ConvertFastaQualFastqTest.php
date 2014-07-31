<?php

namespace Models\Scripts\QIIME;

class ConvertFastaQualFastqTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ConvertFastqQualFastqTest");
	}

	private $mockProject = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}
	public function setUp() {
		$this->object = new \Models\Scripts\QIIME\ConvertFastaQualFastq($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\
	 */
	public function testIncludes() {
		$this->assertTrue(false);
	}
}
