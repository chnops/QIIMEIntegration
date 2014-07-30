<?php

namespace Controllers;

class UploadControllerTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("UploadControllerTest");
	}

	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getStep"))
			->getMock();
		$this->mockWorkflow->expects($this->any())->method("getStep")->will($this->returnValue("upload"));
	}
	public function setUp() {
		$_FILES = array();
		$_POST = array();
		$this->object = new UploadController($this->mockWorkflow);
	}

	/**
	 * @covers \Controllers\UploadController::getSubTitle
	 */
	public function testGetSubTitle() {
		$expected = "Upload Input Files";

		$actual = $this->object->getSubTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\UploadController::retrievePastResults
	 */
	public function testRetrievePastResults_noProjectSelected() {

		$actual = $this->object->retrievePastResults();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\UploadController::retrievePastResults
	 */
	public function testRetrievePastResults_noPreviousFiles() {
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue(array()));
		$this->object->setProject($mockProject);
		
		$actual = $this->object->retrievePastResults();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\UploadController::retrievePastResults
	 */
	public function testRetrievePastResults_onePreviousFile() {
		$files = array(
			"type1" => array(array("name" => "fileName1", "status" => "fileStatus1"),),
		);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($files));
		$oldHelper = \Utils\Helper::getHelper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray", "htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("categorizeArray")->will($this->returnArgument(0));
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new UploadController($this->mockWorkflow);
		$this->object->setProject($mockProject);
		$expected = "<h3>Previously Uploaded files:</h3><div class=\"accordion\">\n" .
			"<h4 onclick=\"hideMe($(this).next())\">type1 files</h4><div><ul>\n" .
			"<li>fileName1 (fileStatus1)</li>\n" .
			"</ul></div>\n</div>";
		
		$actual = $this->object->retrievePastResults();

		\Utils\Helper::setDefaultHelper($oldHelper);
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::retrievePastResults
	 */
	public function testRetrievePastResults_manyPreviousFiles() {
		$files = array(
			"type1" => array(
				array("name" => "fileName1", "status" => "fileStatus1"),
				array("name" => "fileName2", "status" => "fileStatus2"),
			),
			"type2" => array(
				array("name" => "fileName3", "status" => "fileStatus3"),
			),
		);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($files));
		$oldHelper = \Utils\Helper::getHelper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray", "htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("categorizeArray")->will($this->returnArgument(0));
		$mockHelper->expects($this->exactly(3))->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new UploadController($this->mockWorkflow);
		$this->object->setProject($mockProject);
		$expected = "<h3>Previously Uploaded files:</h3><div class=\"accordion\">\n" .
			"<h4 onclick=\"hideMe($(this).next())\">type1 files</h4><div><ul>\n" .
			"<li>fileName1 (fileStatus1)</li>\n" .
			"<li>fileName2 (fileStatus2)</li>\n" .
			"</ul></div>\n" .
			"<h4 onclick=\"hideMe($(this).next())\">type2 files</h4><div><ul>\n" .
			"<li>fileName3 (fileStatus3)</li>\n" .
			"</ul></div>\n" .
			"</div>";
		
		$actual = $this->object->retrievePastResults();

		\Utils\Helper::setDefaultHelper($oldHelper);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\UploadController::parseInput
	 */
	public function testParseInput_notLoggedIn() {
		$expecteds = array(
			"disabled" => " disabled",
			"is_result_error" => true,
			"result" => "In order to upload files, you must be logged in and have a project selected.",
		);
		$actuals = array();

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::parseInput
	 */
	public function testParseInput_loggedInButNoProjectSelected() {
		$expecteds = array(
			"disabled" => " disabled",
			"is_result_error" => true,
			"result" => "In order to upload files, you must be logged in and have a project selected.",
		);
		$actuals = array();
		$this->object->setUsername("username");

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::parseInput
	 */
	public function testParseInput_loggedIn_NoPOST() {
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => false,
			"result" => "",
		);
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType"))
			->getMock();
		$this->object->expects($this->never())->method("getFileType")->will($this->returnValue(false));
		$this->object->setUsername("username");
		$this->object->setProject("project");

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::parseInput
	 */
	public function testParseInput_loggedIn_BadFileType() {
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => true,
			"result" => "The file you uploaded had an unrecognized type.",
		);
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(false));
		$this->object->setUsername("username");
		$this->object->setProject("project");
		$_POST['step'] = "upload";

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::parseInput
	 */
	public function testParseInput_loggedIn_noFileName() {
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => true,
			"result" => "Unable to determine file name",
		);
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType","getFileName"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(true));
		$this->object->expects($this->once())->method("getFileName")->will($this->returnValue(false));
		$this->object->setUsername("username");
		$this->object->setProject("project");
		$_POST['step'] = "upload";

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::parseInput
	 */
	public function testParseInput_fileNameAlreadyExists() {
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => true,
			"result" => "You have already uploaded a file with that file name. File names must be unique",
		);
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType","getFileName","fileNameExists"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(true));
		$this->object->expects($this->once())->method("getFileName")->will($this->returnValue("fileName"));
		$this->object->expects($this->once())->method("fileNameExists")->will($this->returnValue(true));
		$this->object->setUsername("username");
		$this->object->setProject("project");
		$_POST['step'] = "upload";

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::parseInput
	 */
	public function testParseInput_uploadedFile() {
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => false,
			"result" => "",
		);
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType","getFileName","fileNameExists","uploadFile"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(true));
		$this->object->expects($this->once())->method("getFileName")->will($this->returnValue("fileName"));
		$this->object->expects($this->once())->method("fileNameExists")->will($this->returnValue(false));
		$this->object->expects($this->once())->method("uploadFile");
		$this->object->setUsername("username");
		$this->object->setProject("project");
		$_POST['step'] = "upload";
		$_FILES['file'] = array();

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::parseInput
	 */
	public function testParseInput_downloadedFile_throwsGenericException() {
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => true,
			"result" => "message",
		);
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType","getFileName","fileNameExists","uploadFile","downloadFile"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(true));
		$this->object->expects($this->once())->method("getFileName")->will($this->returnValue("fileName"));
		$this->object->expects($this->once())->method("fileNameExists")->will($this->returnValue(false));
		$this->object->expects($this->never())->method("uploadFile");
		$this->object->expects($this->once())->method("downloadFile")->will($this->throwException(new \Exception("message")));
		$this->object->setUsername("username");
		$this->object->setProject("project");
		$_POST['step'] = "upload";
		$_POST['url'] = "http://domain.com";

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	public function testParseInput_downloadedFile_throwsOSException() {
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => true,
			"result" => "message",
		);
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType","getFileName","fileNameExists","uploadFile","downloadFile"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(true));
		$this->object->expects($this->once())->method("getFileName")->will($this->returnValue("fileName"));
		$this->object->expects($this->once())->method("fileNameExists")->will($this->returnValue(false));
		$this->object->expects($this->never())->method("uploadFile");
		$this->object->expects($this->once())->method("downloadFile")->will($this->throwException(new \Models\OperatingSystemException("message")));
		$this->object->setUsername("username");
		$this->object->setProject("project");
		$_POST['step'] = "upload";
		$_POST['url'] = "http://domain.com";

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::parseInput
	 */
	public function testParseInput_downloadedFile_noException() {
		$expecteds = array(
			"disabled" => "",
			"is_result_error" => false,
			"result" => "message",
		);
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType","getFileName","fileNameExists","uploadFile","downloadFile"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(true));
		$this->object->expects($this->once())->method("getFileName")->will($this->returnValue("fileName"));
		$this->object->expects($this->once())->method("fileNameExists")->will($this->returnValue(false));
		$this->object->expects($this->never())->method("uploadFile");
		$this->object->expects($this->once())->method("downloadFile")->will($this->returnValue("message"));
		$this->object->setUsername("username");
		$this->object->setProject("project");
		$_POST['step'] = "upload";
		$_POST['url'] = "http://domain.com";

		$this->object->parseInput();

		$actuals['disabled'] = $this->object->getDisabled();
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Controllers\UploadController::getFileName
	 */
	public function testGetFileName_notDownload_FILESnotSet() {
		$isDownload = false;

		$actual = $this->object->getFileName($isDownload);

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileName
	 */
	public function testGetFileName_notDownload_FILESset() {
		$expected = "fileName";
		$_FILES['file'] = array("name" => $expected);
		$isDownload = false;

		$actual = $this->object->getFileName($isDownload);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileName
	 */
	public function testGetFileName_downloaded_urlNotSet() {
		$expected = "";
		$isDownload = true;

		$actual = $this->object->getFileName($isDownload);

		$this->assertequals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileName
	 */
	public function testGetFileName_downloaded_noNameInUrl() {
		$expected = "";
		$_POST['url'] = "///";
		$isDownload = true;

		$actual = $this->object->getFileName($isDownload);

		$this->assertequals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileName
	 */
	public function testGetFileName_downloaded_nameInUrlFirstPop() {
		$expected = "file.html";
		$_POST['url'] = "http://domain.com/file.html";
		$isDownload = true;

		$actual = $this->object->getFileName($isDownload);

		$this->assertequals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileName
	 */
	public function testGetFileName_downloaded_nameInUrlSecondPop() {
		$expected = "domain.com";
		$_POST['url'] = "http://domain.com/";
		$isDownload = true;

		$actual = $this->object->getFileName($isDownload);

		$this->assertequals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileName
	 */
	public function testGetFileName_downloaded_nameInUrlThirdPop() {
		$expected = "domain.com";
		$_POST['url'] = "http://domain.com//";
		$isDownload = true;

		$actual = $this->object->getFileName($isDownload);

		$this->assertequals($expected, $actual);
	}

	/**
	 * @covers \Controllers\UploadController::fileNameExists
	 */
	public function testFileNameExists_projectNotSet() {

		$actual = $this->object->fileNameExists("fileName");

		$this->assertFalse($actual);
	}
	/**
	 * @covers \Controllers\UploadController::fileNameExists
	 */
	public function testFileNameExists_noUploadedFiles() {
		$projects = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($projects));
		$this->object->setProject($mockProject);

		$actual = $this->object->fileNameExists("fileName");

		$this->assertFalse($actual);
	}
	/**
	 * @covers \Controllers\UploadController::fileNameExists
	 */
	public function testFileNameExists_manyUploadedFiles_nameDoesNotExist() {
		$projects = array(
			array("name" => "fileName1"),
			array("name" => "fileName2"),
			array("name" => "fileName3"),
		);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($projects));
		$this->object->setProject($mockProject);

		$actual = $this->object->fileNameExists("fileName");

		$this->assertFalse($actual);
	}
	/**
	 * @covers \Controllers\UploadController::fileNameExists
	 */
	public function testFileNameExists_manyUploadedFiles_nameIsFirst() {
		$projects = array(
			array("name" => "fileName1"),
			array("name" => "fileName2"),
			array("name" => "fileName3"),
		);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($projects));
		$this->object->setProject($mockProject);

		$actual = $this->object->fileNameExists("fileName1");

		$this->assertTrue($actual);
	}
	/**
	 * @covers \Controllers\UploadController::fileNameExists
	 */
	public function testFileNameExists_manyUploadedFiles_nameIsLast() {
		$projects = array(
			array("name" => "fileName1"),
			array("name" => "fileName2"),
			array("name" => "fileName3"),
		);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($projects));
		$this->object->setProject($mockProject);

		$actual = $this->object->fileNameExists("fileName3");

		$this->assertTrue($actual);
	}

	/**
	 * @covers \Controllers\UploadController::setFileType
	 */
	public function testSetFileType() {
		$expected = new \Models\MapFileType();

		$this->object->setFileType($expected);

		$actual = $this->object->getFileType();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\UploadController::getFileType
	 */
	public function testGetFileType_fileTypeAlreadySet() {
		$expected = new \Models\MapFileType();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getFileTypeFromHtmlId", "getFileTypes"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("getFileTypeFromHtmlId")->will($this->returnValue($expected));
		$mockProject->expects($this->never())->method("getFileTypes")->will($this->returnValue(array($expected)));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new UploadController($mockWorkflow);
		$this->object->setProject($mockProject);
		$this->object->setFileType($expected);

		$actual = $this->object->getFileType();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileType
	 */
	public function testGetFileType_projectNotSet_POSTnotSet() {
		$expected = new \Models\MapFileType();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getFileTypeFromHtmlId", "getFileTypes"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("getFileTypeFromHtmlId")->will($this->returnValue($expected));
		$mockProject->expects($this->once())->method("getFileTypes")->will($this->returnValue(array($expected)));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new UploadController($mockWorkflow);

		$actual = $this->object->getFileType();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileType
	 */
	public function testGetFileType_projectNotSet_POSTset() {
		$_POST['type'] = "map";
		$expected = new \Models\MapFileType();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getFileTypeFromHtmlId", "getFileTypes"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getFileTypeFromHtmlId")->will($this->returnValue($expected));
		$mockProject->expects($this->never())->method("getFileTypes")->will($this->returnValue(array($expected)));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new UploadController($mockWorkflow);

		$actual = $this->object->getFileType();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileType
	 */
	public function testGetFileType_projectSet_POSTnotSet() {
		$expected = new \Models\MapFileType();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getFileTypeFromHtmlId", "getFileTypes"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("getFileTypeFromHtmlId")->will($this->returnValue($expected));
		$mockProject->expects($this->once())->method("getFileTypes")->will($this->returnValue(array($expected)));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new UploadController($mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->getFileType();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getFileType
	 */
	public function testGetFileType_projectSet_POSTset() {
		$_POST['type'] = "map";
		$expected = new \Models\MapFileType();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getFileTypeFromHtmlId", "getFileTypes"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getFileTypeFromHtmlId")->will($this->returnValue($expected));
		$mockProject->expects($this->never())->method("getFileTypes")->will($this->returnValue(array($expected)));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new UploadController($mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->getFileType();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\UploadController::downloadFile
	 */
	public function testDownloadFile_noConsoleOutput() {
		$expected = "File downloaded has started.";
		$oldHelper = \Utils\Helper::getHelper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->never())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("receiveDownloadedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("receiveDownloadedFile")->will($this->returnValue(""));
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(new \Models\MapFileType()));
		$this->object->setProject($mockProject);

		$actual = $this->object->downloadFile("url", "fileName");

		\Utils\Helper::setDefaultHelper($oldHelper);
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::downloadFile
	 */
	public function testDownloadFile_someConsoleOutput() {
		$expected = "File downloaded has started.<br/>The console returned the following output:<br/>message";
		$oldHelper = \Utils\Helper::getHelper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("receiveDownloadedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("receiveDownloadedFile")->will($this->returnValue("message"));
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(new \Models\MapFileType()));
		$this->object->setProject($mockProject);

		$actual = $this->object->downloadFile("url", "fileName");

		\Utils\Helper::setDefaultHelper($oldHelper);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\UploadController::uploadFile
	 * @covers \Controllers\FileUploadError::getErrorMessage
	 * @covers \Controllers\FileUploadError::__construct
	 */
	public function testUploadFile_errorIniSize() {
		$expecteds = array(
			"result" => "There was an error uploading your file: The uploaded file is too large",
			"is_result_error" => true,
		);
		$actuals = array();
		$file = array("error" => UPLOAD_ERR_INI_SIZE);

		$this->object->uploadFile($file);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::uploadFile
	 * @covers \Controllers\FileUploadError::getErrorMessage
	 * @covers \Controllers\FileUploadError::__construct
	 */
	public function testUploadFile_errorFormSize() {
		$expecteds = array(
			"result" => "There was an error uploading your file: The uploaded file is too large",
			"is_result_error" => true,
		);
		$actuals = array();
		$file = array("error" => UPLOAD_ERR_FORM_SIZE);

		$this->object->uploadFile($file);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::uploadFile
	 * @covers \Controllers\FileUploadError::getErrorMessage
	 * @covers \Controllers\FileUploadError::__construct
	 */
	public function testUploadFile_errorPartial() {
		$expecteds = array(
			"result" => "There was an error uploading your file: Something wrong happened on our end.  We'll check it out",
			"is_result_error" => true,
		);
		$actuals = array();
		$file = array("error" => UPLOAD_ERR_PARTIAL);

		$this->object->uploadFile($file);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::uploadFile
	 * @covers \Controllers\FileUploadError::getErrorMessage
	 * @covers \Controllers\FileUploadError::__construct
	 */
	public function testUploadFile_errorNoTmpDir() {
		$expecteds = array(
			"result" => "There was an error uploading your file: Something wrong happened on our end.  We'll check it out",
			"is_result_error" => true,
		);
		$actuals = array();
		$file = array("error" => UPLOAD_ERR_NO_TMP_DIR);

		$this->object->uploadFile($file);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::uploadFile
	 * @covers \Controllers\FileUploadError::getErrorMessage
	 * @covers \Controllers\FileUploadError::__construct
	 */
	public function testUploadFile_errorCannotWrite() {
		$expecteds = array(
			"result" => "There was an error uploading your file: Something wrong happened on our end.  We'll check it out",
			"is_result_error" => true,
		);
		$actuals = array();
		$file = array("error" => UPLOAD_ERR_CANT_WRITE);

		$this->object->uploadFile($file);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::uploadFile
	 * @covers \Controllers\FileUploadError::getErrorMessage
	 * @covers \Controllers\FileUploadError::__construct
	 */
	public function testUploadFile_errorNoFile() {
		$expecteds = array(
			"result" => "There was an error uploading your file: No file was even uploaded",
			"is_result_error" => true,
		);
		$actuals = array();
		$file = array("error" => UPLOAD_ERR_NO_FILE);

		$this->object->uploadFile($file);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::uploadFile
	 * @covers \Controllers\FileUploadError::getErrorMessage
	 * @covers \Controllers\FileUploadError::__construct
	 */
	public function testUploadFile_errorExtension() {
		$expecteds = array(
			"result" => "There was an error uploading your file: That's the wrong file type",
			"is_result_error" => true,
		);
		$actuals = array();
		$file = array("error" => UPLOAD_ERR_EXTENSION);

		$this->object->uploadFile($file);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::uploadFile
	 * @covers \Controllers\FileUploadError::getErrorMessage
	 * @covers \Controllers\FileUploadError::__construct
	 */
	public function testUploadFile_errorUnknown() {
		$expecteds = array(
			"result" => "There was an error uploading your file: An unknown file-upload error occurred.",
			"is_result_error" => true,
		);
		$actuals = array();
		$file = array("error" => UPLOAD_ERR_PARTIAL * 1000);

		$this->object->uploadFile($file);

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::uploadFile
	 */
	public function testUploadFile_noError_projectDoesNotException() {
		$expecteds = array(
			"result" => "File fileName successfully uploaded!",
			"is_result_error" => false,
		);
		$actuals = array();
		$file = array("error" => 0, "name" => "fileName", "tmp_name" => "/tmp/fileName", "size" => 100);
		$oldHelper = \Utils\Helper::getHelper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("receiveUploadedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("receiveUploadedFile");
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(new \Models\MapFileType()));
		$this->object->setProject($mockProject);

		$this->object->uploadFile($file);

		\Utils\Helper::setDefaultHelper($oldHelper);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\UploadController::uploadFile
	 */
	public function testUploadFile_noError_projectThrowsException() {
		$expecteds = array(
			"result" => "message",
			"is_result_error" => true,
		);
		$actuals = array();
		$file = array("error" => 0, "name" => "fileName", "tmp_name" => "/tmp/fileName", "size" => 100);
		$oldHelper = \Utils\Helper::getHelper();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->never())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("receiveUploadedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("receiveUploadedFile")->will($this->throwException(new \Exception("message")));
		$this->object = $this->getMockBuilder('\Controllers\UploadController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("getFileType"))
			->getMock();
		$this->object->expects($this->once())->method("getFileType")->will($this->returnValue(new \Models\MapFileType()));
		$this->object->setProject($mockProject);

		$this->object->uploadFile($file);

		\Utils\Helper::setDefaultHelper($oldHelper);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Controllers\UploadController::renderInstructions
	 */
	public function testRenderInstructions() {

		$actual = $this->object->renderInstructions();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\UploadController::renderForm
	 */
	public function testRenderForm_disabled_projectNotSet() {
		$expected = "
			<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
				<input type=\"hidden\" name=\"step\" value=\"upload\"/>
				<label for=\"file\">Select a file to upload:
				<input type=\"file\" name=\"file\" disabled/></label>
				<label for=\"type\">File type:
				<select name=\"type\" onchange=\"displayHideables(this[this.selectedIndex].getAttribute('value'));\" disabled>" .
				"<option value=\"map\" selected>Map</option>" .
				"</select></label>
			<button type=\"submit\" disabled>Upload</button>
			</form>" .
				"<br/><strong>-OR-</strong><br/>" .
				"<form method=\"POST\" action\"index.php\">
			<input type=\"hidden\" name=\"step\" value=\"upload\"/>
			<label for=\"url\">Specify a url to download file from:
			<input type=\"text\" name=\"url\" value=\"\" placeholder=\"http://seq.center/file/path\" disabled>
			<label for=\"type\">File type:
			<select name=\"type\" onchange=\"displayHideables(this[this.selectedIndex].getAttribute('value'));\" disabled>" .
			"<option value=\"map\" selected>Map</option>" .
			"</select></label>
			<button type=\"submit\" disabled>Download</button>
			</form>";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getFileTypes"))
			->getMockForAbstractClass();
		$mockProject->expects($this->exactly(2))->method("getFileTypes")->will($this->returnValue(array(new \Models\MapFileType())));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getStep", "getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->any())->method("getStep")->will($this->returnValue("upload"));
		$mockWorkflow->expects($this->exactly(2))->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new UploadController($mockWorkflow);
		$this->object->setDisabled(" disabled");

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::renderForm
	 */
	public function testRenderForm_notDisabled_projectSet() {
		$expected = "
			<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
				<input type=\"hidden\" name=\"step\" value=\"upload\"/>
				<label for=\"file\">Select a file to upload:
				<input type=\"file\" name=\"file\"/></label>
				<label for=\"type\">File type:
				<select name=\"type\" onchange=\"displayHideables(this[this.selectedIndex].getAttribute('value'));\">" .
				"<option value=\"map\" selected>Map</option>" .
				"</select></label>
			<button type=\"submit\">Upload</button>
			</form>" .
				"<br/><strong>-OR-</strong><br/>" .
				"<form method=\"POST\" action\"index.php\">
			<input type=\"hidden\" name=\"step\" value=\"upload\"/>
			<label for=\"url\">Specify a url to download file from:
			<input type=\"text\" name=\"url\" value=\"\" placeholder=\"http://seq.center/file/path\">
			<label for=\"type\">File type:
			<select name=\"type\" onchange=\"displayHideables(this[this.selectedIndex].getAttribute('value'));\">" .
			"<option value=\"map\" selected>Map</option>" .
			"</select></label>
			<button type=\"submit\">Download</button>
			</form>";
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getFileTypes"))
			->getMockForAbstractClass();
		$mockProject->expects($this->exactly(2))->method("getFileTypes")->will($this->returnValue(array(new \Models\MapFileType())));
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getStep", "getNewProject"))
			->getMock();
		$mockWorkflow->expects($this->any())->method("getStep")->will($this->returnValue("upload"));
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new UploadController($mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->renderForm();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\UploadController::renderHelp
	 */
	public function testRenderHelp_projectNotSet() {
		$expected = "<p>There are four types of files QIIME uses:
			<ol>
			<li>A map file</li>
			<li>A fasta formatted sequence file</li>
			<li>A sequence quality file</li>
			<li>A fastq sequence-quality file</li>
			</ol></p>" .
			"<div class=\"hideable\" id=\"help_type\">\n" .
			"help</div>\n";
		$mockFileType = $this->getMockBuilder('\Models\MapFileType')
			->setMethods(array("getHtmlId", "renderHelp"))
			->getMockForAbstractClass();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getFileTypes"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockFileType->expects($this->once())->method("getHtmlId")->will($this->returnValue("type"));
		$mockFileType->expects($this->once())->method("renderHelp")->will($this->returnValue("help"));
		$mockProject->expects($this->once())->method("getFileTypes")->will($this->returnValue(array($mockFileType)));
		$mockWorkflow->expects($this->once())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new UploadController($mockWorkflow);

		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::renderHelp
	 */
	public function testRenderHelp_projectSet() {
		$expected = "<p>There are four types of files QIIME uses:
			<ol>
			<li>A map file</li>
			<li>A fasta formatted sequence file</li>
			<li>A sequence quality file</li>
			<li>A fastq sequence-quality file</li>
			</ol></p>" .
			"<div class=\"hideable\" id=\"help_type\">\n" .
			"help</div>\n";
		$mockFileType = $this->getMockBuilder('\Models\MapFileType')
			->setMethods(array("getHtmlId", "renderHelp"))
			->getMockForAbstractClass();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getFileTypes"))
			->getMockForAbstractClass();
		$mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getNewProject"))
			->getMock();
		$mockFileType->expects($this->once())->method("getHtmlId")->will($this->returnValue("type"));
		$mockFileType->expects($this->once())->method("renderHelp")->will($this->returnValue("help"));
		$mockProject->expects($this->once())->method("getFileTypes")->will($this->returnValue(array($mockFileType)));
		$mockWorkflow->expects($this->never())->method("getNewProject")->will($this->returnValue($mockProject));
		$this->object = new UploadController($mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\UploadController::renderSpecificStyle
	 */
	public function testRenderSpecificStyle() {

		$actual = $this->object->renderSpecificStyle();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\UploadController::renderSpecificScript
	 */
	public function testRenderSpecificScript() {
		$fileType = new \Models\MapFileType();
		$htmlId = $fileType->getHtmlId();
		$this->object->setFileType($fileType);
		$expected = "window.onload=function() {window.hideableFields = ['help'];displayHideables('{$htmlId}');};";

		$actual = $this->object->renderSpecificScript();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\UploadController::getScriptLibraries
	 */
	public function testGetScriptLibraries() {

		$actual = $this->object->getScriptLibraries();

		$this->assertEmpty($actual);
	}
}
