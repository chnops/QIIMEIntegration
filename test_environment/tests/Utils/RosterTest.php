<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

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
		$expected = NULL;

		$actual = \Utils\Roster::getRoster();

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Utils\Roster::setDefaultRoster
	 */
	public function testSetDefaultRoster_notNull() {
		$expected = $this->object;

		\Utils\Roster::setDefaultRoster($this->object);

		$actual = \Utils\Roster::getRoster();
		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Utils\Roster::setDefaultRoster
	 */
	public function testSetDefaultRoster_null() {
		$expected = NULL;

		\Utils\Roster::setDefaultRoster(NULL);

		$actual = \Utils\Roster::getRoster();
		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Utils\Roster::createUser
	 */
	public function testCreateUser_databaseFails() {
		$expected = new \Exception("Unable to store new user in database");
		$actual = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUser", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("createDir"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUser")->will($this->returnValue(false));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->never())->method("createDir");
		$this->object = new Roster($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->createUser("username");

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Utils\Roster::createUser
	 */
	public function testCreateUser_operatingSystemFails() {
		$expected = new \Exception("message");
		$actual = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUser", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("createDir"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUser")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("createDir")->will($this->throwException(new \Exception("message")));
		$this->object = new Roster($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->createUser("username");

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Utils\Roster::createUser
	 */
	public function testCreateUser_nothingFails() {
		$expected = true;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUser", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("createDir"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUser")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("createDir");
		$this->object = new Roster($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->createUser("username");

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers \Utils\Roster::userExists
	 */
	public function testUserExists_userDoesExist() {
		$expected = true;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("userExists"))
			->getMock();
		$mockDatabase->expects($this->once())->method("userExists")->will($this->returnValue(true));
		$this->object = new Roster($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->userExists("username");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Utils\Roster::userExists
	 */
	public function testUserExists_userDoesNotExist() {
		$expected = false;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("userExists"))
			->getMock();
		$mockDatabase->expects($this->once())->method("userExists")->will($this->returnValue(false));
		$this->object = new Roster($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->userExists("username");

		$this->assertSame($expected, $actual);
	}
}
