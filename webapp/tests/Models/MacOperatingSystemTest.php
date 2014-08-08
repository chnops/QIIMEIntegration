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
	 * @covers MacOperatingSysmte::isValidFileName
	 */
	public function testIsValidFileName() {
		$this->markTestIncomplete();
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
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		try {

			$this->object->runScript($mockProject, $runId = 1, $mockScript, $mockDatabase);

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
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("renderVersionCommand", "renderCommand"))
			->getMockForAbstractClass();
		$mockDatabase = $this->databaseBuilder
			->setMethods(array("renderCommandRunComplete"))
			->getMock();
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/tmp/file_that_does_not_exist.ext"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		system("mkdir ./projects/u1/");
		try {

			$this->object->runScript($mockProject, $runId = 1, $mockScript, $mockDatabase);

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
		$this->markTestIncomplete();
		// TODO need to check that dir was created, with output, error, and evnironment files
		$expected = "";
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
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("false"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		system("mkdir ./projects/u1/");

		$actual = $this->object->runScript($mockProject, $runId = 1, $mockScript, $mockDatabase);

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
		$this->markTestIncomplete();
		// TODO need to check that dir was created, with output, error, and evnironment files
		$expected = "";
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
		$mockProject->expects($this->once())->method("getProjectDir")->will($this->returnValue("u1"));
		$mockProject->expects($this->once())->method("getEnvironmentSource")->will($this->returnValue("/dev/null"));
		$mockScript->expects($this->once())->method("renderVersionCommand")->will($this->returnValue("printf 'version'"));
		$mockScript->expects($this->once())->method("renderCommand")->will($this->returnValue("printf 'did stuff'"));
		$mockDatabase->expects($this->once())->method("renderCommandRunComplete")->will($this->returnValue("printf 'true'"));
		system("mkdir ./projects/u1/");

		$actual = $this->object->runScript($mockProject, $runId = 1, $mockScript, $mockDatabase);

		$this->assertRegExp('/\d+/', $actual);
	}
}
