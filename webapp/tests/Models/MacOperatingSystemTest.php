<?php

namespace Models;

class MacOperatingSystemTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("MacOperatingSystemTest");
	}

	private $projectBuilder = NULL;
	private $databaseBuilder = NULL;
	private $projectHome = "./projects/";
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->projectBuilder = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor();
		$this->databaseBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor();
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
	public function testUploadFile_moveFails() {
		$expected = "Unable to move file from temporary upload to operating system";
		$actual = "";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1/p1"));
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("moveUploadedFile"))
			->getMock();
		$this->object->expects($this->once())->method("moveUploadedFile")->will($this->returnValue(false));
		try {
	
			$this->object->uploadFile($mockProject, "givenName", "tmpName");

		}
		catch(OperatingSystemException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::uploadFile
	 */
	public function testUploadFile_moveDoesNotFail() {
		$expected = true;
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1/p1"));
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("moveUploadedFile"))
			->getMock();
		$this->object->expects($this->once())->method("moveUploadedFile")->will($this->returnValue(true));
	
		$actual = $this->object->uploadFile($mockProject, "givenName", "tmpName");

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers MacOperatingSystem::downloadFile
	 */
	public function testMoveDownloadedFile() {

		$actual = $this->object->moveUploadedFile("notAnUploadedFile", "notAPathway");

		$this->assertFalse($actual);
	}

	/**
	 * @covers MacOperatingSystem::downloadFile
	 */
	public function testDownloadFile_escapingOccursCorrectly() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers MacOperatingSystem::downloadFile
	 */
	public function testDownloadFile_sourceFails() {
		$expected = new OperatingSystemException("Unable to download file");
		$expected->setConsoleOutput("sh: /tmp/file_that_does_not_exist.fake: No such file or directory\n");
		$actual = NULL;
		$mockProject = $this->projectBuilder
			->setMethods(array("getOwner", "getId", "getEnvironmentSource", "getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->exactly(2))->method("getOwner")->will($this->returnValue("username"));
		$mockProject->expects($this->exactly(2))->method("getId")->will($this->returnValue(1));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue('/tmp/file_that_does_not_exist.fake'));
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandUploadSuccess", "renderCommandUploadFailure"))
			->getMock();
		$mockDatabase->expects($this->once())->method("renderCommandUploadSuccess")->will($this->returnValue("true"));
		$mockDatabase->expects($this->once())->method("renderCommandUploadFailure")->will($this->returnValue("false"));
		$url = "http://localhost/";
		$outputName = "localhost.html";
		try {

			$this->object->downloadFile($mockProject, $url, $outputName, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::downloadFile
	 */
	public function testDownloadFile_cdFails() {
		$expected = new OperatingSystemException("Unable to download file");
		$expected->setConsoleOutput("Unable to find project directory");
		$actual = NULL;
		$mockProject = $this->projectBuilder
			->setMethods(array("getOwner", "getId", "getEnvironmentSource", "getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->exactly(2))->method("getOwner")->will($this->returnValue("username"));
		$mockProject->expects($this->exactly(2))->method("getId")->will($this->returnValue(1));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue('/dev/null'));
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("not_" . "u1"));
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandUploadSuccess", "renderCommandUploadFailure"))
			->getMock();
		$mockDatabase->expects($this->once())->method("renderCommandUploadSuccess")->will($this->returnValue("true"));
		$mockDatabase->expects($this->once())->method("renderCommandUploadFailure")->will($this->returnValue("false"));
		$url = "http://localhost/";
		$outputName = "localhost.html";
		try {

			$this->object->downloadFile($mockProject, $url, $outputName, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::downloadFile
	 */
	public function testDownloadFile_urlDoesNotExist() {
		$expected = new OperatingSystemException("Unable to download file");
		$url = "http://localhost/bad_file_name.ext";
		$outputName = "localhost.html";
		$expected->setConsoleOutput("The requested URL does not exist");
		$actual = NULL;
		system("mkdir ./projects/u1/; mkdir ./projects/u1/uploads/");
		$mockProject = $this->projectBuilder
			->setMethods(array("getOwner", "getId", "getEnvironmentSource", "getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->exactly(2))->method("getOwner")->will($this->returnValue("username"));
		$mockProject->expects($this->exactly(2))->method("getId")->will($this->returnValue(1));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue('/dev/null'));
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandUploadSuccess", "renderCommandUploadFailure"))
			->getMock();
		$mockDatabase->expects($this->once())->method("renderCommandUploadSuccess")->will($this->returnValue("true"));
		$mockDatabase->expects($this->once())->method("renderCommandUploadFailure")->will($this->returnValue("false"));
		try {

			$this->object->downloadFile($mockProject, $url, $outputName, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::downloadFile
	 */
	public function testDownloadFile_wgetDoesNotExist() {
		$expected = new OperatingSystemException("Unable to download file");
		$url = "http://localhost/";
		$outputName = "localhost.html";
		$expected->setConsoleOutput("wget not found");
		$actual = NULL;
		system("mkdir ./projects/u1/; mkdir ./projects/u1/uploads/");
		$mockProject = $this->projectBuilder
			->setMethods(array("getOwner", "getId", "getEnvironmentSource", "getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->exactly(2))->method("getOwner")->will($this->returnValue("username"));
		$mockProject->expects($this->exactly(2))->method("getId")->will($this->returnValue(1));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue('/dev/null'));
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1; alias which=false; cd ."));
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandUploadSuccess", "renderCommandUploadFailure"))
			->getMock();	
		$mockDatabase->expects($this->once())->method("renderCommandUploadSuccess")->will($this->returnValue("true"));
		$mockDatabase->expects($this->once())->method("renderCommandUploadFailure")->will($this->returnValue("false"));
		try {

			$this->object->downloadFile($mockProject, $url, $outputName, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::downloadFile
	 */
	public function testDownloadFile_wgetDoesNotSucceed() {
		$this->markTestIncomplete();
		// This is a hard one to test...
	}
	/**
	 * @covers MacOperatingSystem::downloadFile
	 */
	public function testDownloadFile_wgetStartsSuccessfully() {
		$expected = "";
		$url = "http://localhost/";
		$outputName = "localhost.html";
		system("mkdir ./projects/u1/; mkdir ./projects/u1/uploads/");
		$mockProject = $this->projectBuilder
			->setMethods(array("getOwner", "getId", "getEnvironmentSource", "getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->exactly(2))->method("getOwner")->will($this->returnValue("username"));
		$mockProject->expects($this->exactly(2))->method("getId")->will($this->returnValue(1));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue('/dev/null'));
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandUploadSuccess", "renderCommandUploadFailure"))
			->getMock();	
		$mockDatabase->expects($this->once())->method("renderCommandUploadSuccess")->will($this->returnValue("true"));
		$mockDatabase->expects($this->once())->method("renderCommandUploadFailure")->will($this->returnValue("false"));

		$actual = $this->object->downloadFile($mockProject, $url, $outputName, $mockDatabase);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_badProjectDir() {
		$expected = new OperatingSystemException("Unable to remove file");
		$expected->setConsoleOutput("Unable to find project directory");
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		try {

			$this->object->deleteFile($mockProject, "fileName", $isUploaded = true, $runId = -1);

		}
		catch (OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_uploaded_normalFile() {
		$expecteds = array(
			"function_return" => "",
			"file_still_exists" => '0',
			"dir_still_exists" => '1',
		);
		$actuals = array();
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		system("mkdir ./projects/u1; mkdir ./projects/u1/uploads/; touch ./projects/u1/uploads/fileName.txt");

		$actuals['function_return'] = $this->object->deleteFile($mockProject, "fileName.txt", $isUploaded = true, $runId = -1);

		exec("if [ -a './projects/u1/uploads/fileName.txt' ]; then echo 1; else echo 0; fi; if [ -a './projects/u1/uploads/' ]; then echo 1; else echo 0; fi;", $output);
		$actuals['file_still_exists'] = $output[0];
		$actuals['dir_still_exists'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_generated_normalFile() {
		$expecteds = array(
			"function_return" => "",
			"file_still_exists" => '0',
			"dir_still_exists" => '1',
			"upload_untouched" => '1',
		);
		$actuals = array();
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		system("mkdir ./projects/u1; mkdir ./projects/u1/uploads/; touch ./projects/u1/uploads/fileName.txt;
				mkdir ./projects/u1/r1; touch ./projects/u1/r1/fileName.txt");

		$actuals['function_return'] = $this->object->deleteFile($mockProject, "fileName.txt", $isUploaded = false, $runId = 1);

		exec("if [ -a './projects/u1/r1/fileName.txt' ]; then echo 1; else echo 0; fi;
			if [ -a './projects/u1/r1/' ]; then echo 1; else echo 0; fi;
			if [ -a './projects/u1/uploads/' ]; then echo 1; else echo 0; fi;", $output);
		$actuals['file_still_exists'] = $output[0];
		$actuals['dir_still_exists'] = $output[1];
		$actuals['upload_untouched'] = $output[2];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_generated_nonExistentFile() {
		$expecteds = array(
			"function_return" => "",
			"file_still_exists" => '0',
			"dir_still_exists" => '1',
		);
		$actuals = array();
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		system("mkdir ./projects/u1; mkdir ./projects/u1/r1");

		$actuals['function_return'] = $this->object->deleteFile($mockProject, "fileName.txt", $isUploaded = false, $runId = 1);

		exec("if [ -a './projects/u1/r1/fileName.txt' ]; then echo 1; else echo 0; fi;
			if [ -a './projects/u1/r1/' ]; then echo 1; else echo 0; fi", $output);
		$actuals['file_still_exists'] = $output[0];
		$actuals['dir_still_exists'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_generated_fileOneOfManyInDir() {
		$expecteds = array(
			"function_return" => "",
			"file_still_exists" => '0',
			"dir_still_exists" => '1',
			"other_file_still_exists" => '1',
		);
		$actuals = array();
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		system("mkdir ./projects/u1; mkdir ./projects/u1/r1; mkdir ./projects/u1/r1/dir; touch ./projects/u1/r1/dir/fileName.txt; touch ./projects/u1/r1/dir/fileName2.txt");

		$actuals['function_return'] = $this->object->deleteFile($mockProject, "dir/fileName.txt", $isUploaded = false, $runId = 1);

		exec("if [ -a './projects/u1/r1/dir/fileName.txt' ]; then echo 1; else echo 0; fi;
			if [ -a './projects/u1/r1/dir' ]; then echo 1; else echo 0; fi;
			if [ -a './projects/u1/r1/dir/fileName2.txt' ]; then echo 1; else echo 0; fi", $output);
		$actuals['file_still_exists'] = $output[0];
		$actuals['dir_still_exists'] = $output[1];
		$actuals['other_file_still_exists'] = $output[2];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_generated_fileLastOneInDir() {
		$this->markTestIncomplete();
		$expecteds = array(
			"function_return" => "",
			"file_still_exists" => '0',
			"dir_still_exists" => '0',
		);
		$actuals = array();
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		system("mkdir ./projects/u1; mkdir ./projects/u1/r1; mkdir ./projects/u1/r1/dir; touch ./projects/u1/r1/dir/fileName.txt");

		$actuals['function_return'] = $this->object->deleteFile($mockProject, "dir/fileName.txt", $isUploaded = false, $runId = 1);

		exec("if [ -a './projects/u1/r1/dir/fileName.txt' ]; then echo 1; else echo 0; fi;
			if [ -a './projects/u1/r1/dir' ]; then echo 1; else echo 0; fi", $output);
		$actuals['file_still_exists'] = $output[0];
		$actuals['dir_still_exists'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_generated_fileInNonExistentDir() {
		$expected = new OperatingSystemException("Unable to remove file");
		$expected->setConsoleOutput("");
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		system("mkdir ./projects/u1; mkdir ./projects/u1/r1");
		try {

			$this->object->deleteFile($mockProject, "dir/fileName.txt", $isUploaded = false, $runId = 1);

		}
		catch (OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
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
	public function testRunScript_badProjectDir() {
		$expected = new OperatingSystemException("There was a problem initializing your script");
		$expected->setConsoleOutput("Unable to create run dir");
		$actual = NULL;
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		$runId = 1;
		try {

			$this->object->runScript($mockProject, $runId, $mockScript, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_badEnvironmentSource() {
		$expected = new OperatingSystemException("There was a problem initializing your script");
		$expected->setConsoleOutput("sh: line 4: /tmp/file_that_does_not_exist.ext: No such file or directory\n");
		$actual = NULL;
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/tmp/file_that_does_not_exist.ext"));
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		system("mkdir ./projects/u1/");
		$runId = 1;
		try {

			$this->object->runScript($mockProject, $runId, $mockScript, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_badVersionCommand() {
		$expected = "";
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("false"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		system("mkdir ./projects/u1/");
		$runId = 1;

		$actual = $this->object->runScript($mockProject, $runId, $mockScript, $mockDatabase);

		$this->assertRegExp('/\d+/', $actual);
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_notAbleToStartScript() {
		$this->markTestIncomplete();
		// this one is hard to test
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_ableToStartScript() {
		$expected = "";
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		system("mkdir ./projects/u1/");
		$runId = 1;

		$actual = $this->object->runScript($mockProject, $runId, $mockScript, $mockDatabase);

		$this->assertRegExp('/\d+/', $actual);
	}
}
