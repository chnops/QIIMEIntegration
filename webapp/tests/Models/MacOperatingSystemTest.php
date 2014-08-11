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
		system("cd {$home};", $returnCode);
		$actuals['system_return_code'] = $returnCode;
		$actuals['home'] = $home;
		$this->assertSame($expecteds, $actuals);
	}

	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_valid_allLetters() {
		$expected = true;

		$actual = $this->object->isValidDirName("asdfqwer");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_valid_allDigits() {
		$expected = true;

		$actual = $this->object->isValidDirName(123456);

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_valid_allUnderscores() {
		$expected = true;

		$actual = $this->object->isValidDirName("________");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_valid_combination() {
		$expected = true;

		$actual = $this->object->isValidDirName("uploads_1_");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_invalid_empty() {
		$expected = false;

		$actual = $this->object->isValidDirName("");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_invalid_containsHyphen() {
		$expected = false;

		$actual = $this->object->isValidDirName("uploads_1-");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_invalid_containsUppercase() {
		$expected = false;

		$actual = $this->object->isValidDirName("Uploads_1_");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_invalid_noNameParts() {
		$expected = false;

		$actual = $this->object->isValidDirName("///");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_invalid_firstNamePartFails() {
		$expected = false;

		$actual = $this->object->isValidDirName("pArt1/part2");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_invalid_secondNamePartFails() {
		$expected = false;

		$actual = $this->object->isValidDirName("part1/pArt2");

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::isValidDirName
	 */
	public function testIsValidDirName_invalid_neitherNamePartFails() {
		$expected = true;

		$actual = $this->object->isValidDirName("part1/part2");

		$this->assertSame($expected, $actual);
	}

	/**
	 * @covers MacOperatingSysmte::getFileParts
	 */
	public function testGetFileParts_noDirs() {
		$expected = array("file");
		
		$actual = $this->object->getFileParts("file");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSysmte::getFileParts
	 */
	public function testGetFileParts_dirs() {
		$expected = array("dir", "file");
		
		$actual = $this->object->getFileParts("dir/file");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSysmte::getFileParts
	 */
	public function testGetFileParts_outsideSlashes() {
		$expected = array("dir", "file");
		
		$actual = $this->object->getFileParts("/dir/file/");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSysmte::getFileParts
	 */
	public function testGetFileParts_consecutiveSlashes() {
		$expected = array("dir", "file");
		
		$actual = $this->object->getFileParts("/dir//file/");

		$this->assertEquals($expected, $actual);
	}
		
	/**
	 * @covers MacOperatingSysmte::concatFileNames
	 */
	public function testConcatFileNames_noSlashes() {
		$expected = "file1/file2";

		$actual = $this->object->concatFileNames("file1", "file2");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSysmte::concatFileNames
	 */
	public function testConcatFileNames_outsideSlashes() {
		$expected = "/file1/file2/";

		$actual = $this->object->concatFileNames("/file1", "file2/");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSysmte::concatFileNames
	 */
	public function testConcatFileNames_insideSlashes() {
		$expected = "/file1/file2/";

		$actual = $this->object->concatFileNames("/file1/", "/file2/");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSysmte::concatFileNames
	 */
	public function testConcatFileNames_multipleSlashes() {
		$expected = "/fi/le1/file2/";

		$actual = $this->object->concatFileNames("//fi//le1//", "//file2//");

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers MacOperatingSystem::createDir
	 */
	public function testCreateDir_nameIsNotValid() {
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to create directory"),
			"dir_exists" => 0,
		);
		$fileName = "fileName";
		$expecteds['exception']->setConsoleOutput("Invalid file name: {$fileName}");
		$actual = array();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(false));
		try {
		
			$this->object->createDir($fileName);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		\Utils\Helper::setDefaultHelper(NULL);
		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::createDir
	 */
	public function testCreateDir_dirAlreadyExists() {
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to create directory"),
			"dir_exists" => 1,
		);
		$actuals = array();
		$fileName = "fileName";
		$expecteds['exception']->setConsoleOutput("mkdir returned error code: 1");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->never())->method("htmlentities");
		\Utils\Helper::setDefaultHelper($mockHelper);
		system("mkdir {$this->projectHome}/{$fileName}");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(true));
		try {
		
			$this->object->createDir($fileName);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		\Utils\Helper::setDefaultHelper(NULL);
		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::createDir
	 */
	public function testCreateDir_dirInNonExistentParent() {
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to create directory"),
			"dir_exists" => 0,
		);
		$actuals = array();
		$fileName = "dir/fileName";
		$expecteds['exception']->setConsoleOutput("mkdir returned error code: 1");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->never())->method("htmlentities");
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(true));
		try {
		
			$this->object->createDir($fileName);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		\Utils\Helper::setDefaultHelper(NULL);
		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::createDir
	 */
	public function testCreateDir_succeeds_depth1() {
		$expecteds = array(
			"function_return" => NULL,
			"dir_exists" => 1,
		);
		$actuals = array();
		$fileName = "fileName";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->never())->method("htmlentities");
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(true));
		
		$actuals['function_return'] = $this->object->createDir($fileName);

		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		\Utils\Helper::setDefaultHelper(NULL);
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::createDir
	 */
	public function testCreateDir_succeeds_depth2() {
		$expecteds = array(
			"function_return" => NULL,
			"dir_exists" => '1',
		);
		$actuals = array();
		$fileName = "dir/fileName";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->never())->method("htmlentities");
		\Utils\Helper::setDefaultHelper($mockHelper);
		system("mkdir {$this->projectHome}/dir");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(true));
		
		$actuals['function_return'] = $this->object->createDir($fileName);

		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		\Utils\Helper::setDefaultHelper(NULL);
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_invalidFileName() {
		$expecteds = array(
			"function_return" => false,
			"dir_exists" => 0,
		);
		$fileName = "fileName";
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(false));
		
		$actuals['function_return'] = $this->object->removeDirIfExists($fileName);

		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_fileDoesNotExist() {
		$expecteds = array(
			"function_return" => false,
			"dir_exists" => 0,
		);
		$fileName = "fileName";
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(true));
		
		$actuals['function_return'] = $this->object->removeDirIfExists($fileName);

		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_succeedsDepth1() {
		$expecteds = array(
			"function_return" => true,
			"dir_exists" => 0,
		);
		$actuals = array();
		$fileName = "fileName";
		system("mkdir {$this->projectHome}/{$fileName}");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(true));
		
		$actuals['function_return'] = $this->object->removeDirIfExists($fileName);

		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_succeedsDepth2() {
		$expecteds = array(
			"function_return" => true,
			"dir_exists" => 0,
		);
		$actuals = array();
		$fileName = "dir/fileName";
		system("mkdir {$this->projectHome}/dir/; mkdir {$this->projectHome}/{$fileName}");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(true));
		
		$actuals['function_return'] = $this->object->removeDirIfExists($fileName);

		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::removeDirIfExists
	 */
	public function testRemoveDirIfExists_succeedsNotEmpty() {
		$expecteds = array(
			"function_return" => true,
			"dir_exists" => 0,
		);
		$actuals = array();
		$fileName = "dir/fileName";
		system("mkdir {$this->projectHome}/dir/; mkdir {$this->projectHome}/{$fileName}; touch {$this->projectHome}/{$fileName}/file");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("isValidDirName"))
			->getMock();
		$this->object->expects($this->once())->method("isValidDirName")->will($this->returnValue(true));
		
		$actuals['function_return'] = $this->object->removeDirIfExists($fileName);

		exec("if [ -a '{$this->projectHome}/{$fileName}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['dir_exists'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_dirDoesNotExist() {
		$expected = new OperatingSystemException("Unable to get dir contents");
		$expected->setConsoleOutput("'find' failed with error code: 1");
		$actual = NULL;
		$dirName = "dir";
		try {

			$this->object->getDirContents($dirName);

		}
		catch (OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_dirIsEmpty() {
		$expected = array();
		$dirName = "dir";
		system("mkdir {$this->projectHome}/{$dirName}");

		$actual = $this->object->getDirContents($dirName);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_dirDepth1_onlyFiles() {
		$expected = array("file1", "file2");
		$dirName = "dir";
		$setUpCode = "mkdir {$this->projectHome}/{$dirName};";
		foreach ($expected as $fileName) {
			$setUpCode .= "touch {$this->projectHome}/{$dirName}/{$fileName};";
		}
		system($setUpCode);

		$actual = $this->object->getDirContents($dirName);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_dirDepth1_onlyEmptyDirs() {
		$expected = array("file1", "file2");
		$dirName = "dir";
		$setUpCode = "mkdir {$this->projectHome}/{$dirName};";
		foreach ($expected as $fileName) {
			$setUpCode .= "mkdir {$this->projectHome}/{$dirName}/{$fileName};";
		}
		system($setUpCode);

		$actual = $this->object->getDirContents($dirName);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_dirDepth2_onlyFullDirsOnLevel1() {
		$expected = array("dir1/file1", "dir2/file2");
		$levelOneDirs = array("dir1", "dir2");
		$dirName = "dir";
		$setUpCode = "mkdir {$this->projectHome}/{$dirName};";
		foreach ($levelOneDirs as $levelOneDirName) {
			$setUpCode .= "mkdir {$this->projectHome}/{$dirName}/{$levelOneDirName};";
		}
		foreach ($expected as $fileName) {
			$setUpCode .= "touch {$this->projectHome}/{$dirName}/{$fileName};";
		}
		system($setUpCode);

		$actual = $this->object->getDirContents($dirName);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_dirDepth2_mixedDirsOnLevel1() {
		$expected = array("dir1/file1", "dir2");
		$levelOneDirs = array("dir1", "dir2");
		$dirName = "dir";
		$setUpCode = "mkdir {$this->projectHome}/{$dirName};";
		foreach ($levelOneDirs as $levelOneDirName) {
			$setUpCode .= "mkdir {$this->projectHome}/{$dirName}/{$levelOneDirName};";
		}
		foreach ($expected as $fileName) {
			$setUpCode .= "touch {$this->projectHome}/{$dirName}/{$fileName};";
		}
		system($setUpCode);

		$actual = $this->object->getDirContents($dirName);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers MacOperatingSystem::getDirContents
	 */
	public function testGetDirContents_dirDepth3() {
		$expected = array("dir1/dir1a/file1a", "dir1/file1", "dir2/file2");
		$levelOneDirs = array("dir1", "dir1/dir1a", "dir2");
		$dirName = "dir";
		$setUpCode = "mkdir {$this->projectHome}/{$dirName};";
		foreach ($levelOneDirs as $levelOneDirName) {
			$setUpCode .= "mkdir {$this->projectHome}/{$dirName}/{$levelOneDirName};";
		}
		foreach ($expected as $fileName) {
			$setUpCode .= "touch {$this->projectHome}/{$dirName}/{$fileName};";
		}
		system($setUpCode);

		$actual = $this->object->getDirContents($dirName);

		$this->assertEquals($expected, $actual);
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
		$expected = false;

		$actual = $this->object->moveUploadedFile("notAnUploadedFile", "notAPathway");

		$this->assertSame($expected, $actual);
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
		$url = "http://localhost/";
		$outputName = "localhost.html";
		$mockProject = $this->projectBuilder
			->setMethods(array("getOwner", "getId", "getEnvironmentSource", "getProjectDir"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandUploadSuccess", "renderCommandUploadFailure"))
			->getMock();
		$mockProject->expects($this->exactly(2))->method("getOwner")->will($this->returnValue("username"));
		$mockProject->expects($this->exactly(2))->method("getId")->will($this->returnValue(1));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue('/tmp/file_that_does_not_exist.fake'));
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
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
	public function testDownloadFile_cdFails() {
		$expected = new OperatingSystemException("Unable to download file");
		$expected->setConsoleOutput("Unable to find project directory");
		$actual = NULL;
		$url = "http://localhost/";
		$outputName = "localhost.html";
		$mockProject = $this->projectBuilder
			->setMethods(array("getOwner", "getId", "getEnvironmentSource", "getProjectDir"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandUploadSuccess", "renderCommandUploadFailure"))
			->getMock();
		$mockProject->expects($this->exactly(2))->method("getOwner")->will($this->returnValue("username"));
		$mockProject->expects($this->exactly(2))->method("getId")->will($this->returnValue(1));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue('/dev/null'));
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("not_" . "u1"));
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
	public function testDownloadFile_urlDoesNotExist() {
		$expected = new OperatingSystemException("Unable to download file");
		$expected->setConsoleOutput("The requested URL does not exist");
		$actual = NULL;
		$url = "http://localhost/bad_file_name.ext";
		$outputName = "localhost.html";
		$mockProject = $this->projectBuilder
			->setMethods(array("getOwner", "getId", "getEnvironmentSource", "getProjectDir"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandUploadSuccess", "renderCommandUploadFailure"))
			->getMock();
		$mockProject->expects($this->exactly(2))->method("getOwner")->will($this->returnValue("username"));
		$mockProject->expects($this->exactly(2))->method("getId")->will($this->returnValue(1));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue('/dev/null'));
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockDatabase->expects($this->once())->method("renderCommandUploadSuccess")->will($this->returnValue("true"));
		$mockDatabase->expects($this->once())->method("renderCommandUploadFailure")->will($this->returnValue("false"));
		system("mkdir ./projects/u1/; mkdir ./projects/u1/uploads/");
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
		$expected->setConsoleOutput("wget not found");
		$actual = NULL;
		$url = "http://localhost/";
		$outputName = "localhost.html";
		$mockProject = $this->projectBuilder
			->setMethods(array("getOwner", "getId", "getEnvironmentSource", "getProjectDir"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandUploadSuccess", "renderCommandUploadFailure"))
			->getMock();	
		$mockProject->expects($this->exactly(2))->method("getOwner")->will($this->returnValue("username"));
		$mockProject->expects($this->exactly(2))->method("getId")->will($this->returnValue(1));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue('/dev/null'));
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1; alias which=false; cd ."));
		$mockDatabase->expects($this->once())->method("renderCommandUploadSuccess")->will($this->returnValue("true"));
		$mockDatabase->expects($this->once())->method("renderCommandUploadFailure")->will($this->returnValue("false"));
		system("mkdir ./projects/u1/; mkdir ./projects/u1/uploads/");
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
		$this->markTestIncomplete();
		//TODO can't check if file actually downloads, because it is started in background
		$expecteds = array(
			"function_return" => "",
		);
		$actuals = array();
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

		$actuals['function_return'] = $this->object->downloadFile($mockProject, $url, $outputName, $mockDatabase);

		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @coverse MacOperatingSystem::findFileName
	 */
	public function testFindFileName_upload() {
		$expectedProjectDir = "u1/p1";
		$expectedRunDir = "uploads";
		$expectedFileName = "fileName.ext";
		$expected = $this->projectHome . $expectedProjectDir . "/" . $expectedRunDir . "/" . $expectedFileName;
		$runDirInput = -1;
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue($expectedProjectDir));

		$actual = $this->object->findFileName($mockProject, $expectedFileName, $runDirInput);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @coverse MacOperatingSystem::findFileName
	 */
	public function testFindFileName_run() {
		$expectedProjectDir = "u1/p1";
		$expectedRunDir = "r1";
		$expectedFileName = "fileName.ext";
		$expected = $this->projectHome . $expectedProjectDir . "/" . $expectedRunDir . "/" . $expectedFileName;
		$runDirInput = 1;
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getProjectDir"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue($expectedProjectDir));

		$actual = $this->object->findFileName($mockProject, $expectedFileName, $runDirInput);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @coverse MacOperatingSystem::getFileExistsCode
	 */
	public function testGetFileExistsCode_noQuotesToEscape() {
		$expected = "if [ ! -e 'fileName' ]; then printf 'The requested file does not exist.'; exit 1; fi;";

		$actual = $this->object->getFileExistsCode("fileName");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @coverse MacOperatingSystem::getFileExistsCode
	 */
	public function testGetFileExistsCode_quotesToEscape() {
		$expected = "if [ ! -e 'file'\''Name' ]; then printf 'The requested file does not exist.'; exit 1; fi;";

		$actual = $this->object->getFileExistsCode("file'Name");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @coverse MacOperatingSystem::getFileExistsCode
	 */
	public function testGetFileExistsCode_execution_fileDoesExist() {
		$expecteds = array(
			"return_code" => 0,
			"console_output" => "",
		);
		$actuals = array();
		system("touch '{$this->projectHome}/file'\''Name'");
		$code = $this->object->getFileExistsCode($this->projectHome . "/file'Name");
		ob_start();

		system($code, $returnCode);

		$actuals['return_code'] = $returnCode;
		$actuals['console_output'] = ob_get_clean();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @coverse MacOperatingSystem::getFileExistsCode
	 */
	public function testGetFileExistsCode_execution_fileDoesNotExist() {
		$expecteds = array(
			"return_code" => 1,
			"console_output" => "The requested file does not exist.",
		);
		$actuals = array();
		$code = $this->object->getFileExistsCode("fileName");
		ob_start();

		system($code, $returnCode);

		$actuals['return_code'] = $returnCode;
		$actuals['console_output'] = ob_get_clean();
		$this->assertEquals($expecteds, $actuals);
	}
		
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_nonDir_doesExist() {
		$expecteds = array(
			"function_return" => NULL,
			"file_exists_after" => "0",
		);
		$actuals = array();
		$fileName = $this->projectHome . "fileName";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("touch {$fileName}");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));

		$actuals['function_return'] = $this->object->deleteFile($mockProject, $fileName, $runId = -1);

		exec("if [ -e '{$fileName}']; then echo '1'; else echo '0'; fi;", $output);
		$actuals['file_exists_after'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_nonDir_doesNotExist() {
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to remove file"),
			"file_exists_after" => "0",
		);
		$expecteds['exception']->setConsoleOutput("The requested file does not exist.");
		$actuals = array();
		$fileName = $this->projectHome . "fileName";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));
		try {

			$this->object->deleteFile($mockProject, $fileName, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec("if [ -e '{$fileName}']; then echo '1'; else echo '0'; fi;", $output);
		$actuals['file_exists_after'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_emptyDir_doesExist() {
		$expecteds = array(
			"function_return" => NULL,
			"file_exists_after" => "0",
		);
		$actuals = array();
		$fileName = $this->projectHome . "dirName";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("mkdir {$fileName}");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));

		$actuals['function_return'] = $this->object->deleteFile($mockProject, $fileName, $runId = -1);

		exec("if [ -e '{$fileName}']; then echo '1'; else echo '0'; fi;", $output);
		$actuals['file_exists_after'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::deleteFile
	 */
	public function testDeleteFile_fullDir_doesExist() {
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to remove file"),
			"file_exists_after" => "0",
		);
		$actuals = array();
		$fileName = $this->projectHome . "fileName";
		$expecteds['exception']->setConsoleOutput("rmdir: {$fileName}: Directory not empty\n");
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("mkdir {$fileName}; touch {$fileName}/file");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));
		try {

			$this->object->deleteFile($mockProject, $fileName, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec("if [ -e '{$fileName}']; then echo '1'; else echo '0'; fi;", $output);
		$actuals['file_exists_after'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers MacOperatingSystem::unzipFile
	 */
	public function testUnzipFile_fileDoesNotExist() {
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to unzip file"),
			"zip_file_exists_after" => "0",
		);
		$actuals = array();
		$zipFile = $this->projectHome . "zipper.zip";
		$expecteds['exception']->setConsoleOutput("The requested file does not exist.");
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($zipFile));
		try {

			$this->object->unzipFile($mockProject, $zipFile, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec("if [ -e '{$zipFile}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['zip_file_exists_after'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::unzipFile
	 */
	public function testUnzipFile_emptyZipFile() {
		$this->markTestIncomplete();
		$expecteds = array(
			"function_return" => array("zipper/file1.ext", "zipper/file2.ext"),
			"zip_file_exists_after" => "0",
		);
		$actuals = array();
		$zipFile = $this->projectHome . "zipper.zip";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("mkdir {$this->projectHome}/zipper; touch {$this->projectHome}/zipper/file1.ext;" .
			"touch {$this->projectHome}/zipper/file2.ext; zip {$zipFile} {$this->projectHome}/zipper/*;" . 
			"rm -r {$this->projectHome}/zipper;");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($zipFile));

		$actuals['function_return'] = $this->object->unzipFile($mockProject, $zipFile, $runId = -1);

		exec("if [ -e '{$zipFile}' ]; then echo '1'; else echo '0'; fi;", $output);
		$actuals['zip_file_exists_after'] = $output[0];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::unzipFile
	 */
	public function testUnzipFile_nonEmptyZipFile() {
		$this->markTestIncomplete();
		$zipFile = "zipper.zip";
		$makeZipCode = "mkdir zipper; touch zipper/file1.ext; touch zipper/file2.ext; zip {$zipFile} zipper/*";
	}

	/**
	 * @covers MacOperatingSystem::compressFile
	 */
	public function testCompressFile_fileDoesNotExist() {
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to compress file"),
			"compressed_file_exists_after" => "0",
			"uncompressed_file_exists_after" => "0",
		);
		$actuals = array();
		$fileName = $this->projectHome . "fileName.ext";
		$expecteds['exception']->setConsoleOutput("The requested file does not exist.");
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));
		try {

			$this->object->compressFile($mockProject, $fileName, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec(
			"if [ -e '{$fileName}.gz' ]; then echo '1'; else echo '0'; fi;" . 
			"if [ -e '{$fileName}' ]; then echo '1'; else echo '0'; fi;" ,
			$output);
		$actuals['compressed_file_exists_after'] = $output[0];
		$actuals['uncompressed_file_exists_after'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::compressFile
	 */
	public function testCompressFile_fileAlreadyCompressed() {
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to compress file"),
			"compressed_file_exists_after" => "1",
			"uncompressed_file_exists_after" => "0",
		);
		$actuals = array();
		$fileName = $this->projectHome . "fileName.ext";
		$expecteds['exception']->setConsoleOutput("gzip: {$fileName}.gz already has .gz suffix -- unchanged");
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("touch {$fileName}.gz");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName . ".gz"));
		try {

			$this->object->compressFile($mockProject, $fileName, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec(
			"if [ -e '{$fileName}.gz' ]; then echo '1'; else echo '0'; fi;" . 
			"if [ -e '{$fileName}' ]; then echo '1'; else echo '0'; fi;" ,
			$output);
		$actuals['compressed_file_exists_after'] = $output[0];
		$actuals['uncompressed_file_exists_after'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::compressFile
	 */
	public function testCompressFile_filePostCompressionNameAlreadyExists() {
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to compress file"),
			"compressed_file_exists_after" => "1",
			"uncompressed_file_exists_after" => "1",
		);
		$actuals = array();
		$fileName = $this->projectHome . "fileName.ext";
		$expecteds['exception']->setConsoleOutput("gzip: {$fileName}.gz already exists;	not overwritten");
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("touch {$fileName}; touch {$fileName}.gz");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));
		try {

			$this->object->compressFile($mockProject, $fileName, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec(
			"if [ -e '{$fileName}.gz' ]; then echo '1'; else echo '0'; fi;" . 
			"if [ -e '{$fileName}' ]; then echo '1'; else echo '0'; fi;" ,
			$output);
		$actuals['compressed_file_exists_after'] = $output[0];
		$actuals['uncompressed_file_exists_after'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	} /**
	 * @covers MacOperatingSystem::compressFile
	 */
	public function testCompressFile_fileDoesExist_nothingFails() {
		$expectedFileName = $this->projectHome . "fileName.ext";
		$expecteds = array(
			"function_return" => $expectedFileName . ".gz",
			"compressed_file_exists_after" => "1",
			"uncompressed_file_exists_after" => "0",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("touch {$expectedFileName};");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($expectedFileName));

		$actuals['function_return'] = $this->object->compressFile($mockProject, $expectedFileName, $runId = -1);

		exec(
			"if [ -e '{$expectedFileName}.gz' ]; then echo '1'; else echo '0'; fi;" . 
			"if [ -e '{$expectedFileName}' ]; then echo '1'; else echo '0'; fi;" ,
			$output);
		$actuals['compressed_file_exists_after'] = $output[0];
		$actuals['uncompressed_file_exists_after'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers MacOperatingSystem::decompressFile
	 */
	public function testDecompressFile_fileDoesNotExist() { 
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to decompress file"),
			"compressed_file_exists_after" => "0",
			"uncompressed_file_exists_after" => "0",
		);
		$actuals = array();
		$uncompressedFileName = $this->projectHome . "fileName.ext";
		$fileName = $uncompressedFileName . ".gz";
		$expecteds['exception']->setConsoleOutput("The requested file does not exist.");
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));
		try {

			$this->object->decompressFile($mockProject, $fileName, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec(
			"if [ -e '{$fileName}' ]; then echo '1'; else echo '0'; fi;" . 
			"if [ -e '{$uncompressedFileName}' ]; then echo '1'; else echo '0'; fi;" ,
			$output);
		$actuals['compressed_file_exists_after'] = $output[0];
		$actuals['uncompressed_file_exists_after'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::decompressFile
	 */
	public function testDecompressFile_fileAlreadyDecompressed() { 
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to decompress file"),
			"compressed_file_exists_after" => "1",
			"uncompressed_file_exists_after" => "0",
		);
		$actuals = array();
		$uncompressedFileName = $this->projectHome . "fileName.ext";
		$fileName = $uncompressedFileName . ".gz";
		$expecteds['exception']->setConsoleOutput("\ngzip: {$fileName}: unexpected end of file");
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("touch {$fileName}");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));
		try {

			$this->object->decompressFile($mockProject, $fileName, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec(
			"if [ -e '{$fileName}' ]; then echo '1'; else echo '0'; fi;" . 
			"if [ -e '{$uncompressedFileName}' ]; then echo '1'; else echo '0'; fi;" ,
			$output);
		$actuals['compressed_file_exists_after'] = $output[0];
		$actuals['uncompressed_file_exists_after'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::decompressFile
	 */
	public function testDecompressFile_fileIsIncorrectlyFormatted() { 
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to decompress file"),
			"compressed_file_exists_after" => "1",
			"uncompressed_file_exists_after" => "0",
		);
		$actuals = array();
		$uncompressedFileName = $this->projectHome . "fileName.ext";
		$fileName = $uncompressedFileName . ".gz";
		$expecteds['exception']->setConsoleOutput("\ngzip: {$fileName}: unexpected end of file");
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("touch {$fileName}");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));
		try {

			$this->object->decompressFile($mockProject, $fileName, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec(
			"if [ -e '{$fileName}' ]; then echo '1'; else echo '0'; fi;" . 
			"if [ -e '{$uncompressedFileName}' ]; then echo '1'; else echo '0'; fi;" ,
			$output);
		$actuals['compressed_file_exists_after'] = $output[0];
		$actuals['uncompressed_file_exists_after'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::decompressFile
	 */
	public function testDecompressFile_fileDecompressNameConflict() { 
		$expecteds = array(
			"exception" => new OperatingSystemException("Unable to decompress file"),
			"compressed_file_exists_after" => "1",
			"uncompressed_file_exists_after" => "1",
		);
		$actuals = array();
		$uncompressedFileName = $this->projectHome . "fileName.ext";
		$fileName = $uncompressedFileName . ".gz";
		$expecteds['exception']->setConsoleOutput("gzip: {$uncompressedFileName} already exists;	not overwritten");
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("echo 'Hello, world!' > {$uncompressedFileName}; gzip {$uncompressedFileName}; touch {$uncompressedFileName}");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));
		try {

			$this->object->decompressFile($mockProject, $fileName, $runId = -1);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		exec(
			"if [ -e '{$fileName}' ]; then echo '1'; else echo '0'; fi;" . 
			"if [ -e '{$uncompressedFileName}' ]; then echo '1'; else echo '0'; fi;" ,
			$output);
		$actuals['compressed_file_exists_after'] = $output[0];
		$actuals['uncompressed_file_exists_after'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::decompressFile
	 */
	public function testDecompressFile_fileDecompressesNothingFails() { 
		$expecteds = array(
			"function_return" => "",
			"compressed_file_exists_after" => "0",
			"uncompressed_file_exists_after" => "1",
		);
		$actuals = array();
		$uncompressedFileName = $this->projectHome . "fileName.ext";
		$fileName = $uncompressedFileName . ".gz";
		$expecteds['function_return'] = $uncompressedFileName;
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		system("echo 'Hello, world!' > {$uncompressedFileName}; gzip {$uncompressedFileName}");
		$this->object = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("findFileName"))
			->getMock();
		$this->object->expects($this->once())->method("findFileName")->will($this->returnValue($fileName));

		$actuals['function_return'] = $this->object->decompressFile($mockProject, $fileName, $runId = -1);

		exec(
			"if [ -e '{$fileName}' ]; then echo '1'; else echo '0'; fi;" . 
			"if [ -e '{$uncompressedFileName}' ]; then echo '1'; else echo '0'; fi;" ,
			$output);
		$actuals['compressed_file_exists_after'] = $output[0];
		$actuals['uncompressed_file_exists_after'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_projectDirDoesNotExist() {
		$expecteds = array(
			"exception" => new OperatingSystemException("There was a problem initializing your script"),
			"run_dir_exists" => "0",
			"env_exists" => "0",
			"error_log_exists" => "0",
			"output_exists" => "0",
		);
		$expecteds['exception']->setConsoleOutput("The requested file does not exist.");
		$actuals = array();
		$projectDir = "u1";
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue($projectDir));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		try {

			$this->object->runScript($mockProject, $runId = 1, $mockScript, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		$runDir = $this->projectHome . "/" . $projectDir . "/r" . $runId;
		exec("
			if [ -a '{$runDir}' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/env.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/error_log.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/output.txt' ]; then echo '1'; else echo '0'; fi;
			", $output);
		$actuals['run_dir_exists'] = $output[0];
		$actuals['env_exists'] = $output[1];
		$actuals['error_log_exists'] = $output[2];
		$actuals['output_exists'] = $output[3];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_makeRunDirFails() {
		$expecteds = array(
			"exception" => new OperatingSystemException("There was a problem initializing your script"),
			"run_dir_exists" => "1",
			"env_exists" => "0",
			"error_log_exists" => "0",
			"output_exists" => "0",
		);
		$expecteds['exception']->setConsoleOutput("Unable to create run dir");
		$actuals = array();
		$projectDir = "u1";
		$runId = 1;
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue($projectDir));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		system("mkdir {$this->projectHome}/{$projectDir};mkdir {$this->projectHome}/{$projectDir}/r{$runId}");
		try {

			$this->object->runScript($mockProject, $runId, $mockScript, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		$runDir = $this->projectHome . "/" . $projectDir . "/r" . $runId;
		exec("
			if [ -a '{$runDir}' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/env.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/error_log.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/output.txt' ]; then echo '1'; else echo '0'; fi;
			", $output);
		$actuals['run_dir_exists'] = $output[0];
		$actuals['env_exists'] = $output[1];
		$actuals['error_log_exists'] = $output[2];
		$actuals['output_exists'] = $output[3];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_badEnvironmentSource() {
		$expecteds = array(
			"exception" => new OperatingSystemException("There was a problem initializing your script"),
			"run_dir_exists" => "1",
			"env_exists" => "0",
			"error_log_exists" => "0",
			"output_exists" => "0",
		);
		$expecteds['exception']->setConsoleOutput("sh: line 3: /tmp/file_that_does_not_exist.ext: No such file or directory\n");
		$actuals = array();
		$projectDir = "u1";
		$runId = 1;
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue($projectDir));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/tmp/file_that_does_not_exist.ext"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		system("mkdir {$this->projectHome}/{$projectDir}");
		try {

			$this->object->runScript($mockProject, $runId, $mockScript, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		$runDir = $this->projectHome . "/" . $projectDir . "/r" . $runId;
		exec("
			if [ -a '{$runDir}' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/env.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/error_log.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/output.txt' ]; then echo '1'; else echo '0'; fi;
			", $output);
		$actuals['run_dir_exists'] = $output[0];
		$actuals['env_exists'] = $output[1];
		$actuals['error_log_exists'] = $output[2];
		$actuals['output_exists'] = $output[3];
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_badVersionCommand() {
		$expecteds = array(
			"run_dir_exists" => "1",
			"env_exists" => "1",
			"version_changes_to_env.txt" => array(),
			"error_log_exists" => "1",
			"error_log_contents" => "There was a problem getting this script's version",
			"output_exists" => "1",
			"output_contents" => "did stuff. altered database.",
		);
		$expectedFunctionReturnRegex = '/\d+/';
		$actuals = array();
		$projectDir = "u1";
		$runId = 1;
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue($projectDir));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("false"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff. '"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'altered database. '"));
		system("mkdir {$this->projectHome}/{$projectDir}");

		$actualFunctionReturn = $this->object->runScript($mockProject, $runId, $mockScript, $mockDatabase);

		$runDir = $this->projectHome . "/" . $projectDir . "/r" . $runId;
		exec("
			if [ -a '{$runDir}' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/env.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/error_log.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/error_log.txt' ]; then cat '{$runDir}/error_log.txt'; else echo ''; fi;
			if [ -a '{$runDir}/output.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/output.txt' ]; then cat '{$runDir}/output.txt'; else echo ''; fi;
			", $output);
		$actuals['run_dir_exists'] = $output[0];
		$actuals['env_exists'] = $output[1];
		$actuals['error_log_exists'] = $output[2];
		$actuals['error_log_contents'] = $output[3];
		$actuals['output_exists'] = $output[4];
		$actuals['output_contents'] = $output[5];
		exec("cd {$runDir}; printenv > .expected; sort .expected > .tmp; mv .tmp .expected;
			cat env.txt > .actual; sort .actual > .tmp; mv .tmp .actual;
			diff .expected .actual; rm .expected .actual", $output2);
		$actuals['version_changes_to_env.txt'] = $output2;
		$this->assertEquals($expecteds, $actuals);
		$this->assertRegExp($expectedFunctionReturnRegex, $actualFunctionReturn);
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_notAbleToStartScript() {
		$expecteds = array(
			"exception" => new OperatingSystemException("There was a problem initializing your script"),
			"run_dir_exists" => "1",
			"env_exists" => "1",
			"version_changes_to_env.txt" => array('19a20', '> version'),
			"error_log_exists" => "1",
			"error_log_contents" => "",
			"output_exists" => "1",
			"output_contents" => "did stuff.",
		);
		$actuals = array();
		$projectDir = "u1";
		$runId = 1;
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue($projectDir));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff. '"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue(") > output.txt 2>> error_log.txt;jobs &> /dev/null;\n("));
		system("mkdir {$this->projectHome}/{$projectDir}");
		try {

			$this->object->runScript($mockProject, $runId, $mockScript, $mockDatabase);

		}
		catch(OperatingSystemException $ex) {
			$actuals['exception'] = $ex;
		}
		$runDir = $this->projectHome . "/" . $projectDir . "/r" . $runId;
		exec("
			if [ -a '{$runDir}' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/env.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/error_log.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/error_log.txt' ]; then cat '{$runDir}/error_log.txt'; echo; else echo ''; fi;
			if [ -a '{$runDir}/output.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/output.txt' ]; then cat '{$runDir}/output.txt'; else echo ''; fi;
			", $output);
		$actuals['run_dir_exists'] = $output[0];
		$actuals['env_exists'] = $output[1];
		$actuals['error_log_exists'] = $output[2];
		$actuals['error_log_contents'] = $output[3];
		$actuals['output_exists'] = $output[4];
		$actuals['output_contents'] = $output[5];
		exec("cd {$runDir}; printenv > .expected; sort .expected > .tmp; mv .tmp .expected;
			cat env.txt > .actual; sort .actual > .tmp; mv .tmp .actual;
			diff .expected .actual; rm .expected .actual", $output2);
		$actuals['version_changes_to_env.txt'] = $output2;
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers MacOperatingSystem::runScript
	 */
	public function testRunScript_ableToStartScript() {
		$expecteds = array(
			"run_dir_exists" => "1",
			"env_exists" => "1",
			"version_changes_to_env.txt" => array('19a20', '> version'),
			"error_log_exists" => "1",
			"error_log_contents" => "",
			"output_exists" => "1",
			"output_contents" => "did stuff. altered database.",
		);
		$actuals = array();
		$expectedFunctionReturnRegex = '/\d+/';
		$projectDir = "u1";
		$runId = 1;
		$mockProject = $this->projectBuilder
			->setMethods(array("getProjectDir", "getEnvironmentSource"))
			->getMockForAbstractClass();
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue($projectDir));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff. '"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'altered database.'"));
		system("mkdir {$this->projectHome}/{$projectDir}");

		$actualFunctionReturn = $this->object->runScript($mockProject, $runId, $mockScript, $mockDatabase);

		$runDir = $this->projectHome . "/" . $projectDir . "/r" . $runId;
		exec("
			if [ -a '{$runDir}' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/env.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/error_log.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/error_log.txt' ]; then cat '{$runDir}/error_log.txt'; echo; else echo ''; fi;
			if [ -a '{$runDir}/output.txt' ]; then echo '1'; else echo '0'; fi;
			if [ -a '{$runDir}/output.txt' ]; then cat '{$runDir}/output.txt'; else echo ''; fi;
			", $output);
		$actuals['run_dir_exists'] = $output[0];
		$actuals['env_exists'] = $output[1];
		$actuals['error_log_exists'] = $output[2];
		$actuals['error_log_contents'] = $output[3];
		$actuals['output_exists'] = $output[4];
		$actuals['output_contents'] = $output[5];
		exec("cd {$runDir}; printenv > .expected; sort .expected > .tmp; mv .tmp .expected;
			cat env.txt > .actual; sort .actual > .tmp; mv .tmp .actual;
			diff .expected .actual; rm .expected .actual", $output2);
		$actuals['version_changes_to_env.txt'] = $output2;
		$this->assertEquals($expecteds, $actuals);
		$this->assertRegExp($expectedFunctionReturnRegex, $actualFunctionReturn);
	}
}
