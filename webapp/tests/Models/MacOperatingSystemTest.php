<?php

namespace Models;

class MacOperatingSystemTest extends \PHPUnit_Framework_TestCase {

	private $newHome = "./data/projects";
	private $operatingSystem = NULL;

	public function setUp() {
		$this->operatingSystem = new MacOperatingSystem();
		$this->operatingSystem->overwriteHome($this->newHome);
	}

	/**
	 * @test
	 * @covers MacOperatingSystem::getHome
	 * @covers MacOperatingSystem::overwriteHome
	 */
	public function testFindsHome() {
		$returnCode = 0;
		system("cd {$this->operatingSystem->getHome()}", $returnCode);
		$this->assertEquals(0, $returnCode);
		$this->assertEquals($this->newHome, $this->operatingSystem->getHome());
	}

	/**
	 * @test
	 * @covers MacOperatingSystem::isValidFileName
	 */
	public function testIsValidFileName() {
		$this->assertFalse($this->operatingSystem->isValidFileName("notAnInt"));
		$this->assertFalse($this->operatingSystem->isValidFileName("one"));
		$this->assertFalse($this->operatingSystem->isValidFileName(0));
		$this->assertFalse($this->operatingSystem->isValidFileName(-1));
		$this->assertFalse($this->operatingSystem->isValidFileName(000000000000));

		$this->assertTrue($this->operatingSystem->isValidFileName(1));
		$this->assertTrue($this->operatingSystem->isValidFileName(2));
		$this->assertTrue($this->operatingSystem->isValidFileName(99999999999));
	}

	/**
	 * @test
	 * @covers MacOperatingSystem::createDir
	 */
	public function testCreateDir() {
		try {
			$this->operatingSystem->createDir("notAValidFileName");
		}
		catch (OperatingSystemException $ex) {
			$this->assertEquals("Invalid file name: notAValidFileName", $ex->getMessage());
		}

		try {
			$this->operatingSystem->createDir(1);
		}
		catch (OperatingSystemException $ex) {
			$this->fail("Failed to create directory {$this->newHome}/1: " . $ex->getMessage());
		}

		try {
			$this->operatingSystem->createDir(1);
		}
		catch (OperatingSystemException $ex) {
			$this->assertEquals("mkdir failed: 1", $ex->getMessage());
		}
		system("rmdir {$this->newHome}/1");

		try {
			$this->operatingSystem->createDir("1");
		}
		catch (OperatingSystemException $ex) {
			$this->fail("Failed to create directory {$this->newHome}/\"1\": " . $ex->getMessage());
		}
		system("rmdir {$this->newHome}/1");

		system("mkdir {$this->newHome}/2");
		try {
			$this->operatingSystem->createDir("2/1");
		}
		catch (OperatingSystemException $ex) {
			$this->fail("Failed to create directory {$this->newHome}/2/1: " . $ex->getMessage());
		}
		system("rmdir {$this->newHome}/2/1;
			rmdir {$this->newHome}/2");

		try {
			$this->operatingSystem->createDir("2/notAValidFileName");
		}
		catch (OperatingSystemException $ex) {
			$this->assertEquals("Invalid file name: 2/notAValidFileName", $ex->getMessage());
		}
	}

	/**
	 * @test
	 * @covers MacOperatingSystem::executeArbitraryScript
	 */
	public function testExecuteArbitraryScript() {
		$this->markTestIncomplete();
	}
}
