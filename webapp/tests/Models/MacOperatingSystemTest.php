<?php

namespace Models;

class MacOperatingSystemTest extends \PHPUnit_Framework_TestCase {

	private $projectHome = "./projects/";
	private $object = NULL;

	public static function setUpBeforeClass() {
		error_log("MacOperatingSystemTest");
	}

	public function setUp() {
		$this->object = new MacOperatingSystem();
		system('rm -rf ./projects/*');
	}

	/**
	 * @covers MacOperatingSystem::getHome
	 */
	public function testGetHome_systemConfiguredCorrectly() {
		$expecteds = array(
			'system_return_code' => 0,
			'home' => $this->projectHome,
		);
		$actuals = array();

		$home = $this->object->getHome();

		$returnCode = 1;
		system("cd {$home}", $returnCode);
		$actuals['system_return_code'] = $returnCode;
		$actuals['home'] = $home;
		$this->assertSame($expecteds, $actuals);
	}

	/**
	 * @covers MacOperatingSystem::isValidFileName
	 */
	public function testIsValidFileName_inWhitelist() {
		$expected = true;

		$actual = $this->object->isValidFileName("uploads");
		
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidFileName
	 */
	public function testIsValidFileName_notInWhitelist() {
		$expected = false;

		$actual = $this->object->isValidFileName("upload");
		
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidFileName
	 */
	public function testIsValidFileName_validLetterUOneDigit() {
		$expected = true;

		$actual = $this->object->isValidFileName("u1");
		
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidFileName
	 */
	public function testIsValidFileName_validLetterXManyDigits() {
		$expected = true;

		$actual = $this->object->isValidFileName("X123456789");
		
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidFileName
	 */
	public function testIsValidFileName_LetterAfterDigits() {
		$expected = false;

		$actual = $this->object->isValidFileName("123456789X");
		
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidFileName
	 */
	public function testIsValidFileName_noLetter() {
		$expected = false;

		$actual = $this->object->isValidFileName("123456789");
		
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidFileName
	 */
	public function testIsValidFileName_noDigits() {
		$expected = false;

		$actual = $this->object->isValidFileName("u");
		
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidFileName
	 */
	public function testIsValidFileName_containsWhiteSpace() {
		$expected = false;

		$actual = $this->object->isValidFileName("u 123456789");
		
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers MacOperatingSystem::createDir
	 * @expectedException \Models\OperatingSystemException
	 */
	public function testCreateDir_unNested_invalidName() {
		$this->object->createDir("notAValidFileName");

		$this->fail("createDir should have thrown an exception");
	}
	/**
	 * @covers MacOperatingSystem::createDir
	 */
	public function testCreateDir_unNested_validNameDoesNotExist() {
		$expecteds = array(
			'dir_name' => "u1",
			'return_code' => 0,
		);
		$actuals = array();

		$this->object->createDir($expecteds['dir_name']);

		$returnCode = 1;
		ob_start();
		system("ls {$this->projectHome}", $returnCode);
		$actuals['dir_name'] = trim(ob_get_clean());
		$actuals['return_code'] = $returnCode;
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::createDir
	 * @expectedException \Models\OperatingSystemException
	 */
	public function testCreateDir_unNested_validNameButAlreadyExists() {
		$dirName = "u1";
		system("mkdir {$this->projectHome}{$dirName}");
		
		$this->object->createDir($dirName);

		$this->fail("createDir should have thrown an exception");
	}
	/**
	 * @covers MacOperatingSystem::createDir
	 * @expectedException \Models\OperatingSystemException
	 */
	public function testCreateDir_nested_invalidName() {
		$invalidName = "u1/p1/r 1";

		$this->object->createDir($invalidName);

		$this->fail("createDir should have thrown an exception");
	}
	/**
	 * @covers MacOperatingSystem::createDir
	 * @expectedException \Models\OperatingSystemException
	 */
	public function testCreateDir_nested_validNamePathDoesNotExist() {
		$nonExistentPath = "u1/p1";

		$this->object->createDir($nonExistentPath);

		$this->fail("createDir should have thrown an exception");
	}
	/**
	 * @covers MacOperatingSystem::createDir
	 */
	public function testRemoveDirIfExists_nested_validNamePathDoesExist() {
		$expected = "p1";
		$existentPath = "u1/p1";
		system("mkdir {$this->projectHome}u1");

		$this->object->createDir($existentPath);

		ob_start();
		system("ls {$this->projectHome}u1/");
		$actual = trim(ob_get_clean());
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_unNested_invalidName() {
		$expected = false;

		$actual = $this->object->removeDirIfExists("notAValidFileName");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_unNested_validNameButDoesNotExist() {
		$expected = false;
		$dirName = "u1";

		$actual = $this->object->removeDirIfExists($dirName);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_unNested_validNameAlreadyExists() {
		$expecteds = array(
			'function_return' => true,
			'dir_in_output' => false,
		);
		$actuals = array();
		$dirName = "u1";
		system("mkdir {$this->projectHome}{$dirName}");
		
		$actuals['function_return'] = $this->object->removeDirIfExists($dirName);

		ob_start();
		system("ls -R {$this->projectHome}");
		$actuals['dir_in_output'] = (ob_get_clean() != "");
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_nested_invalidName() {
		$expected = false;
		$invalidName = "u1/p1/r 1";

		$actual = $this->object->removeDirIfExists($invalidName);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_nested_validNamePathDoesNotExist() {
		$expected = false;
		$nonExistentPath = "u1/p1";

		$actual = $this->object->removeDirIfExists($nonExistentPath);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_unNested_validNamePathDoesExist() {
		$expecteds = array(
			'function_return' => true,
			'dir_in_output' => false,
		);
		$actuals = array();
		system("mkdir {$this->projectHome}u1; mkdir {$this->projectHome}u1/p1");
		$existentPath = "u1/p1";

		$actuals['function_return'] = $this->object->removeDirIfExists($existentPath);

		ob_start();
		system("ls {$this->projectHome}/u1/");
		$actuals['dir_in_output'] = (ob_get_clean() != "");
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_emptyDir_inHome() {
		$emptyDir = "u1";
		system("mkdir {$this->projectHome}{$emptyDir}");

		$actuals = $this->object->getDirContents($emptyDir, $prependHome = true);

		$this->assertEmpty($actuals);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_oneLevelDir_inHome() {
		$oneLevelDir = "u1";
		$files = array ("p1", "p2", "p3");
		$commandString = "mkdir {$this->projectHome}{$oneLevelDir};";
		foreach ($files as $file) {
			$commandString .= "touch {$this->projectHome}{$oneLevelDir}/{$file};";
		}
		system($commandString);

		$actuals = $this->object->getDirContents($oneLevelDir, $prependHome = true);

		$this->assertEquals($files, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_twoLevelDir_inHome() {
		$twoLevelDir = "u1";
		$files = array ("p1" => "r1", "p2" => "r2", "p3" => "r3");
		$commandString = "mkdir {$this->projectHome}{$twoLevelDir};";
		$expecteds = array();
		foreach ($files as $dir => $file) {
			$commandString .= "mkdir {$this->projectHome}{$twoLevelDir}/{$dir}; touch {$this->projectHome}{$twoLevelDir}/{$dir}/{$file};";
			$expecteds[] = $dir . "/" . $file;
		}
		system($commandString);

		$actuals = $this->object->getDirContents($twoLevelDir, $prependHome = true);

		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_hiddenFiles_inHome() {
		$files = array("u1", "u2", ".u3");
		$expecteds = array("u1", "u2");
		$commandString = "cd {$this->projectHome};";
		foreach ($files as $file) {
			$commandString .= "touch {$file};";
		}
		system($commandString);

		$actuals = $this->object->getDirContents("", $prependHome = true);

		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_nonEmpty_outOfHome() {
		$outOfHomeDir = "/tmp/phpunit_dir";
		$files = array("u1", "u2", "u3");
		$commandString = "mkdir {$outOfHomeDir};";
		foreach ($files as $file) {
			$commandString .= "touch {$outOfHomeDir}/{$file};";
		}
		system($commandString);

		$actuals = $this->object->getDirContents($outOfHomeDir, $prependHome = false);

		system("rm -r {$outOfHomeDir}");
		$this->assertEquals($files, $actuals);
	}

	/**
	 * @covers MacOperatingSystem::uploadFile
	 */
	public function testUploadFile() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers MacOperatingSystem::downloadFile
	 */
	public function testDownloadFile() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers MacOperatingSystem::unzipFile
	 */
	public function testUnzipFile() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers MacOperatingSystem::compressFile
	 */
	public function testCompressFile() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers MacOperatingSystem::decompressFile
	 */
	public function testDecompressFile() { 
		$this->markTestIncomplete();
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript() {
		$this->markTestIncomplete();
	}
}
