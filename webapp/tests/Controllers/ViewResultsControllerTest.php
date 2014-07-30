<?php

namespace Controllers;

class ViewResultsControllerTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ViewResultsControllerTest");
	}

	private $mockWorkflow = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockWorkflow = $this->getMockBuilder('\Models\QIIMEWorkflow')
			->disableOriginalConstructor()
			->setMethods(array("getStep"))
			->getMock();
		$this->mockWorkflow->expects($this->any())->method("getStep")->will($this->returnValue("view"));
	}
	public function setUp() {
		$_POST = array();
		$this->object = new ViewResultsController($this->mockWorkflow);
	}

	/**
	 * @covers \Controllers\ViewResultsController::getSubTitle
	 */
	public function testGetSubTitle() {
		$expected = "View Results";

		$actual = $this->object->getSubTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\ViewResultsController::retrievePastResults
	 */
	public function testRetrievePastResults_noProjectSelected() {
		$expected = "<p>In order to view results, you must <a href=\"?step=login\">log in</a> and <a href=\"?step=select\">select a project</a></p>";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->never())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setProject(NULL);

		$actual = $this->object->retrievePastResults();

		\Utils\Helper::setDefaultHelper(NULL);
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\ViewResultsController::retrievePastResults
	 */
	public function testRetrievePastResults_projectSelected() {
		$expected = "<h3>name</h3><ul>
			<li>Owner: owner</li>
			<li>Unique id: 1</li>
			</ul>" .
			"<hr/>You can see a preview of the file you wish to download here:<br/>
			<div class=\"file_example\" id=\"file_preview\"></div>";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->exactly(3))->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("getName", "getOwner", "getId"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("getName")->will($this->returnValue("name"));
		$mockProject->expects($this->once())->method("getOwner")->will($this->returnValue("owner"));
		$mockProject->expects($this->once())->method("getId")->will($this->returnValue(1));
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->retrievePastResults();

		\Utils\Helper::setDefaultHelper(NULL);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_notLoggedIn() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "You have not selected a project, therefore there are no results to view.",
		);
		$actuals = array();

		$this->object->parseInput();

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_loggedInButNoProjectSelected() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "You have not selected a project, therefore there are no results to view.",
		);
		$actuals = array();
		$this->object->setUsername("username");
		$_POST['action'] = "doSomething";

		$this->object->parseInput();

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_loggedIn_noPOST() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "",
		);
		$actuals = array();
		$this->object->setUsername("username");
		$this->object->setProject("project");

		$this->object->parseInput();

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_loggedIn_runIsNotNumeric() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Run id must be numeric",
		);
		$actuals = array();
		$this->object->setUsername("username");
		$this->object->setProject("project");
		$_POST['file'] = "fileName";
		$_POST['action'] = "doSomething";
		$_POST['run'] = "NOT_NUMERIC";

		$this->object->parseInput();

		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_loggedIn_badAction() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "An invalid action was requested: doSomething",
		);
		$actuals = array();
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->exactly(2))->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject("project");
		$_POST['action'] = "doSomething";
		$_POST['file'] = "fileName";
		$_POST['run'] = 1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_delete_isUploaded_fails() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Unable to delete 'fileName': message",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("deleteUploadedFile", "deleteGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("deleteUploadedFile")->will($this->throwException(new \Exception("message")));
		$mockProject->expects($this->never())->method("deleteGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "delete";
		$_POST['file'] = "fileName";
		$_POST['run'] = -1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_delete_isUploaded_succeeds() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "File deleted: fileName",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("deleteUploadedFile", "deleteGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("deleteUploadedFile");
		$mockProject->expects($this->never())->method("deleteGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "delete";
		$_POST['file'] = "fileName";
		$_POST['run'] = -1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_delete_isNotUploaded_fails() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Unable to delete 'fileName': message",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("deleteUploadedFile", "deleteGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("deleteUploadedFile");
		$mockProject->expects($this->once())->method("deleteGeneratedFile")->will($this->throwException(new \Exception("message")));
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "delete";
		$_POST['file'] = "fileName";
		$_POST['run'] = 1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_delete_isNotUploaded_succeeds() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "File deleted: fileName",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("deleteUploadedFile", "deleteGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("deleteUploadedFile");
		$mockProject->expects($this->once())->method("deleteGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "delete";
		$_POST['file'] = "fileName";
		$_POST['run'] = 1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_unzip_isUploaded_fails() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Unable to unzip 'fileName': message",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("unzipUploadedFile", "unzipGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("unzipUploadedFile")->will($this->throwException(new \Exception("message")));
		$mockProject->expects($this->never())->method("unzipGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "unzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = -1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_unzip_isUploaded_succeeds() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "Unzipped file: fileName",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("unzipUploadedFile", "unzipGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("unzipUploadedFile");
		$mockProject->expects($this->never())->method("unzipGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "unzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = -1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_unzip_isNotUploaded_fails() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Unable to unzip 'fileName': message",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("unzipUploadedFile", "unzipGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("unzipUploadedFile");
		$mockProject->expects($this->once())->method("unzipGeneratedFile")->will($this->throwException(new \Exception("message")));
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "unzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = 1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_unzip_isNotUploaded_succeeds() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "Unzipped file: fileName",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("unzipUploadedFile", "unzipGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("unzipUploadedFile");
		$mockProject->expects($this->once())->method("unzipGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "unzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = 1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_gzip_isUploaded_fails() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Unable to compress 'fileName': message",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("compressUploadedFile", "compressGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("compressUploadedFile")->will($this->throwException(new \Exception("message")));
		$mockProject->expects($this->never())->method("compressGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "gzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = -1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_gzip_isUploaded_succeeds() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "File compressed: fileName",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("compressUploadedFile", "compressGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("compressUploadedFile");
		$mockProject->expects($this->never())->method("compressGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "gzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = -1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_gzip_isNotUploaded_fails() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Unable to compress 'fileName': message",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("compressUploadedFile", "compressGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("compressUploadedFile");
		$mockProject->expects($this->once())->method("compressGeneratedFile")->will($this->throwException(new \Exception("message")));
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "gzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = 1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_gzip_isNotUploaded_succeeds() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "File compressed: fileName",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("compressUploadedFile", "compressGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("compressUploadedFile");
		$mockProject->expects($this->once())->method("compressGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "gzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = 1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_gunzip_isUploaded_fails() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Unable to de-compress 'fileName': message",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("decompressUploadedFile", "decompressGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("decompressUploadedFile")->will($this->throwException(new \Exception("message")));
		$mockProject->expects($this->never())->method("decompressGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "gunzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = -1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_gunzip_isUploaded_succeeds() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "File de-compressed: fileName",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("decompressUploadedFile", "decompressGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("decompressUploadedFile");
		$mockProject->expects($this->never())->method("decompressGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "gunzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = -1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_gunzip_isNotUploaded_fails() {
		$expecteds = array(
			"is_result_error" => true,
			"result" => "Unable to de-compress 'fileName': message",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("decompressUploadedFile", "decompressGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("decompressUploadedFile");
		$mockProject->expects($this->once())->method("decompressGeneratedFile")->will($this->throwException(new \Exception("message")));
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "gunzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = 1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Controllers\ViewResultsController::parseInput
	 */
	public function testParseInput_gunzip_isNotUploaded_succeeds() {
		$expecteds = array(
			"is_result_error" => false,
			"result" => "File de-compressed: fileName",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("decompressUploadedFile", "decompressGeneratedFile"))
			->getMockForAbstractClass();
		$mockProject->expects($this->never())->method("decompressUploadedFile");
		$mockProject->expects($this->once())->method("decompressGeneratedFile");
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setUsername("username");
		$this->object->setProject($mockProject);
		$_POST['action'] = "gunzip";
		$_POST['file'] = "fileName";
		$_POST['run'] = 1;

		$this->object->parseInput();

		\Utils\Helper::setDefaultHelper(NULL);
		$actuals['is_result_error'] = $this->object->isResultError();
		$actuals['result'] = $this->object->getResult();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Controllers\ViewResultsController::renderInstructions
	 */
	public function testRenderInstructions() {

		$actual = $this->object->renderInstructions();

		$this->assertEmpty($actual);
	}

	/**
	 * @covers \Controllers\ViewResultsController::renderForm
	 */
	public function testRenderForm_projectNotSet() {
		$this->object->setProject(NULL);

		$actual = $this->object->renderForm();

		$this->assertEmpty($actual);
	}
	/**
	 * @covers \Controllers\ViewResultsController::renderForm
	 */
	public function testRenderForm_zeroUploadedFiles_zeroGeneratedFiles() {
		$expected = "";
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray"))
			->getMock();
		$mockHelper->expects($this->never())->method("categorizeArray")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue(array()));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue(array()));
		$this->object = new ViewResultsController($this->mockWorkflow);
		$this->object->setProject($mockProject);

		$actual = $this->object->renderForm();

		\Utils\Helper::setDefaultHelper(NULL);
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\ViewResultsController::renderForm
	 */
	public function testRenderForm_zeroUploadedFiles_manyGeneratedFiles() {
		$expected = "<h3>Generated Files:</h3><div class=\"accordion\">\n" .
			"<h4 onclick=\"hideMe($(this).next())\">files from run 1</h4><div><table>\n01</table></div>\n" . 
			"<h4 onclick=\"hideMe($(this).next())\">files from run 2</h4><div><table>\n2</table></div>\n" .
			"</div>\n";
		$generatedFiles = array(
			1 => array(
				array("name" => "name1", "run_id" => 1),
				array("name" => "name2", "run_id" => 1),
			),
			2 => array(
				array("name" => "name3", "run_id" => 2),
			),
		);
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray"))
			->getMock();
		$mockHelper->expects($this->once())->method("categorizeArray")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue(array()));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue($generatedFiles));
		$this->object = $this->getMockBuilder('\Controllers\ViewResultsController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("renderFileMenu"))
			->getMock();
		$this->object->expects($this->exactly(3))->method("renderFileMenu")->will($this->returnArgument(0));
		$this->object->setProject($mockProject);

		$actual = $this->object->renderForm();

		\Utils\Helper::setDefaultHelper(NULL);
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\ViewResultsController::renderForm
	 */
	public function testRenderForm_manyUploadedFiles_zeroGeneratedFiles() {
		$expected = "<h3>Uploaded Files:</h3><div class=\"accordion\">\n" .
			"<h4 onclick=\"hideMe($(this).next())\">map files</h4><div><table>\n01</table></div>\n" . 
			"<h4 onclick=\"hideMe($(this).next())\">sequence files</h4><div><table>\n2</table></div>\n" .
			"</div>\n";
		$uploadedFiles = array(
			"map" => array(
				array("name" => "name1", "status" => -1, "size" => 100),
				array("name" => "name2", "status" => -1, "size" => 100),
			),
			"sequence" => array(
				array("name" => "name3", "status" => -1, "size" => 100),
			),
		);
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray"))
			->getMock();
		$mockHelper->expects($this->once())->method("categorizeArray")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($uploadedFiles));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue(array()));
		$this->object = $this->getMockBuilder('\Controllers\ViewResultsController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("renderFileMenu"))
			->getMock();
		$this->object->expects($this->exactly(3))->method("renderFileMenu")->will($this->returnArgument(0));
		$this->object->setProject($mockProject);

		$actual = $this->object->renderForm();

		\Utils\Helper::setDefaultHelper(NULL);
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\ViewResultsController::renderForm
	 */
	public function testRenderForm_manyUploadedFiles_manyGeneratedFiles() {
		$expected = "<h3>Uploaded Files:</h3><div class=\"accordion\">\n" .
			"<h4 onclick=\"hideMe($(this).next())\">map files</h4><div><table>\n01</table></div>\n" . 
			"<h4 onclick=\"hideMe($(this).next())\">sequence files</h4><div><table>\n2</table></div>\n" .
			"</div>\n" .
		"<h3>Generated Files:</h3><div class=\"accordion\">\n" .
			"<h4 onclick=\"hideMe($(this).next())\">files from run 1</h4><div><table>\n34</table></div>\n" . 
			"<h4 onclick=\"hideMe($(this).next())\">files from run 2</h4><div><table>\n5</table></div>\n" .
			"</div>\n";
		$uploadedFiles = array(
			"map" => array(
				array("name" => "name1", "status" => -1, "size" => 100),
				array("name" => "name2", "status" => -1, "size" => 100),
			),
			"sequence" => array(
				array("name" => "name3", "status" => -1, "size" => 100),
			),
		);
		$generatedFiles = array(
			1 => array(
				array("name" => "name1", "run_id" => 1),
				array("name" => "name2", "run_id" => 1),
			),
			2 => array(
				array("name" => "name3", "run_id" => 2),
			),
		);
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("categorizeArray"))
			->getMock();
		$mockHelper->expects($this->exactly(2))->method("categorizeArray")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles"))
			->getMockForAbstractClass();
		$mockProject->expects($this->once())->method("retrieveAllUploadedFiles")->will($this->returnValue($uploadedFiles));
		$mockProject->expects($this->once())->method("retrieveAllGeneratedFiles")->will($this->returnValue($generatedFiles));
		$this->object = $this->getMockBuilder('\Controllers\ViewResultsController')
			->setConstructorArgs(array($this->mockWorkflow))
			->setMethods(array("renderFileMenu"))
			->getMock();
		$this->object->expects($this->exactly(6))->method("renderFileMenu")->will($this->returnArgument(0));
		$this->object->setProject($mockProject);

		$actual = $this->object->renderForm();

		\Utils\Helper::setDefaultHelper(NULL);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\ViewResultsController::renderFileMenu
	 */
	public function testRenderFileMenu_sizeUncertain() {
		$expected = "<tr class=\"ready\" id=\"result_file_1\"><td>fileName (ready) (<em>size uncertain</em>)</td>
			<td><a class=\"button\" onclick=\"previewFile('download.php?file_name=fileName&run=-1&as_text=true')\">Preview</a></td>
			<td><a class=\"button\" onclick=\"window.location='download.php?file_name=fileName&run=-1'\">Download</a></td>
			<td><a class=\"button more\" onclick=\"$(this).parents('tr').next().toggle('highlight', {}, 500);$(this).parents('tr').next().next().toggle('highlight', {}, 500);\">More...</a></td></tr>
<tr><td>&nbsp;</td><td><form action=\"#result_file_1\" method=\"POST\" onsubmit=\"return confirm('Are you sure you want to delete this file? Action cannot be undone');\"><input type=\"hidden\" name=\"run\" value=\"-1\"><input type=\"hidden\" name=\"file\" value=\"fileName\"><input type=\"submit\" name=\"action\" value=\"delete\"></form></td>
<td><form action=\"#result_file_1\" method=\"POST\"><input type=\"hidden\" name=\"run\" value=\"-1\"><input type=\"hidden\" name=\"file\" value=\"fileName\"><input type=\"submit\" name=\"action\" value=\"gzip\"></form></td>
<td><form action=\"#result_file_1\" method=\"POST\"><input type=\"hidden\" name=\"run\" value=\"-1\"><input type=\"hidden\" name=\"file\" value=\"fileName\"><input type=\"submit\" name=\"action\" value=\"unzip\"></form></td>
</tr><tr><td>&nbsp;</td><td>&nbsp;</td><td><form action=\"#result_file_1\" method=\"POST\"><input type=\"hidden\" name=\"run\" value=\"-1\"><input type=\"hidden\" name=\"file\" value=\"fileName\"><input type=\"submit\" name=\"action\" value=\"gunzip\"></form></td>
<td>&nbsp;</td></tr>";
		$rowHtmlId = 1;
		$fileName = "fileName";
		$fileStatus = "ready";
		$fileSize = -1;
		$runId = -1;

		$actual = $this->object->renderFileMenu($rowHtmlId, $fileName, $fileStatus, $fileSize, $runId);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\ViewResultsController::renderFileMenu
	 */
	public function testRenderFileMenu_sizeCertain() {
		$expected = "<tr class=\"ready\" id=\"result_file_1\"><td>fileName (ready) (<em>size: 1088B</em>)</td>
			<td><a class=\"button\" onclick=\"previewFile('download.php?file_name=fileName&run=-1&as_text=true')\">Preview</a></td>
			<td><a class=\"button\" onclick=\"window.location='download.php?file_name=fileName&run=-1'\">Download</a></td>
			<td><a class=\"button more\" onclick=\"$(this).parents('tr').next().toggle('highlight', {}, 500);$(this).parents('tr').next().next().toggle('highlight', {}, 500);\">More...</a></td></tr>
<tr><td>&nbsp;</td><td><form action=\"#result_file_1\" method=\"POST\" onsubmit=\"return confirm('Are you sure you want to delete this file? Action cannot be undone');\"><input type=\"hidden\" name=\"run\" value=\"-1\"><input type=\"hidden\" name=\"file\" value=\"fileName\"><input type=\"submit\" name=\"action\" value=\"delete\"></form></td>
<td><form action=\"#result_file_1\" method=\"POST\"><input type=\"hidden\" name=\"run\" value=\"-1\"><input type=\"hidden\" name=\"file\" value=\"fileName\"><input type=\"submit\" name=\"action\" value=\"gzip\"></form></td>
<td><form action=\"#result_file_1\" method=\"POST\"><input type=\"hidden\" name=\"run\" value=\"-1\"><input type=\"hidden\" name=\"file\" value=\"fileName\"><input type=\"submit\" name=\"action\" value=\"unzip\"></form></td>
</tr><tr><td>&nbsp;</td><td>&nbsp;</td><td><form action=\"#result_file_1\" method=\"POST\"><input type=\"hidden\" name=\"run\" value=\"-1\"><input type=\"hidden\" name=\"file\" value=\"fileName\"><input type=\"submit\" name=\"action\" value=\"gunzip\"></form></td>
<td>&nbsp;</td></tr>";
		$rowHtmlId = 1;
		$fileName = "fileName";
		$fileStatus = "ready";
		$fileSize = 1088;
		$runId = -1;

		$actual = $this->object->renderFileMenu($rowHtmlId, $fileName, $fileStatus, $fileSize, $runId);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Controllers\ViewResultsController::renderHelp
	 */
	public function testRenderHelp() {
		$expected = "<p>Here is the moment you've been waiting for... your results! From this page, you can preview, download, and manage any of the files that
			you have uploaded or generated by running scripts.</p>";
		
		$actual = $this->object->renderHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\ViewResultsController::renderSpecifiStyle
	 */
	public function testRenderSpecificStyle() {
		$expected = "div#file_preview{margin:.75em;display:none}
			div.form table{border-collapse:collapse;margin:0px;width:100%}
			div.form td{padding:.5em;white-space:nowrap}
			div.form tr{background-color:#FFF6B2}
			div.form tr:nth-child(6n+1){background-color:#FFFFE0}
			div.form tr:nth-child(6n+2){background-color:#FFFFE0}
			div.form tr:nth-child(6n+3){background-color:#FFFFE0}
			div.form button{padding:.25em;margin:.25em;font-size:.80em}";

		$actual = $this->object->renderSpecificStyle();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\ViewResultsController::renderSpecificScript
	 */
	public function testRenderSpecificScript() {
		$expected = "function previewFile(url){
			var displayDiv = $('#file_preview');
			displayDiv.css('display', 'block');
			displayDiv.load(url);}
			$(function() {
				$('div.form td').each(function() {
					$(this).width($(this).width());
				});
				$('a.more').click();
				var hash = window.location.hash;
				if(hash) {
					$(hash + ' a.more').click();
				}
			});";

		$actual = $this->object->renderSpecificScript();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Controllers\ViewResultsController::getScriptLibraries
	 */
	public function testGetScriptLibraries() {

		$actual = $this->object->getScriptLibraries();

		$this->assertEmpty($actual);
	}
}
