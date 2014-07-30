<?php

namespace Utils;

class RosterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("RosterTest");
	}

	private $mockDatabase = NULL;
	private $mockOperatingSystem = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->getMock();
		$this->mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->getMock();
	}
	public function setUp() {
		\Utils\Roster::setDefaultRoster(NULL);
		$this->object = new Roster($this->mockDatabase, $this->mockOperatingSystem);
	}

	/**
	 * @covers \Utils\Roster::getRoster
	 */
	public function testGetRoster() {

		$actual = \Utils\Roster::getRoster();

		$this->assertNull($actual);
	}

	/**
	 * @covers \Utils\Roster::setDefaultRoster
	 */
	public function testSetDefaultRoster_notNull() {

		\Utils\Roster::setDefaultRoster($this->object);

		$actual = \Utils\Roster::getRoster();
		$this->assertSame($this->object, $actual);
	}
	/**
	 * @covers \Utils\Roster::setDefaultRoster
	 */
	public function testSetDefaultRoster_null() {

		\Utils\Roster::setDefaultRoster(NULL);

		$actual = \Utils\Roster::getRoster();
		$this->assertNull($actual);
	}

	/**
	 * @covers \Utils\Roster::createUser
	 * @expectedException \Exception
	 */
	public function testCreateUser_databaseFails() {
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUser", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUser")->will($this->returnValue(false));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("createDir"))
			->getMock();
		$mockOperatingSystem->expects($this->never())->method("createDir");
		$this->object = new Roster($mockDatabase, $mockOperatingSystem);

		$this->object->createUser("username");

		$this->fail("createUser should have thrown an exception");
	}
	/**
	 * @covers \Utils\Roster::createUser
	 * @expectedException \Exception
	 */
	public function testCreateUser_operatingSystemFails() {
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUser", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUser")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("createDir"))
			->getMock();
		$mockOperatingSystem->expects($this->once())->method("createDir")->will($this->throwException(new \Exception()));
		$this->object = new Roster($mockDatabase, $mockOperatingSystem);

		$this->object->createUser("username");

		$this->fail("createUser should have thrown an exception");
	}
	/**
	 * @covers \Utils\Roster::createUser
	 */
	public function testCreateUser_nothingFails() {
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUser", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUser")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("createDir"))
			->getMock();
		$mockOperatingSystem->expects($this->once())->method("createDir");
		$this->object = new Roster($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->createUser("username");

		$this->assertTrue($actual);
	}

	/**
	 * @covers \Utils\Roster::userExists
	 */
	public function testUserExists_userDoesExist() {
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("userExists"))
			->getMock();
		$mockDatabase->expects($this->once())->method("userExists")->will($this->returnValue(true));
		$this->object = new Roster($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->userExists("username");

		$this->assertTrue($actual);
	}
	/**
	 * @covers \Utils\Roster::userExists
	 */
	public function testUserExists_userDoesNotExist() {
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("userExists"))
			->getMock();
		$mockDatabase->expects($this->once())->method("userExists")->will($this->returnValue(false));
		$this->object = new Roster($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->userExists("username");

		$this->assertFalse($actual);
	}
}
