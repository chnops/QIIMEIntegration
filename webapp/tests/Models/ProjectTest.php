<?php

namespace Models;
use Models\Scripts\ScriptException;

class ProjectTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ProjectTest");
	}

	private $owner = "asharp";
	private $id = 1;
	private $name = "Proj1";

	private $newOwner = "bsharp";
	private $newId = 2;
	private $newName = "Proj2";

	private $mockDatabase = NULL;
	private $mockOperatingSystem = NULL;
	private $object= NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->getMock();
		$this->mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->getMock();
	}

	public function setUp() {
		$this->object = new QIIMEProject($this->mockDatabase, $this->mockOperatingSystem);
	}

	/**
	 * @covers \Models\DefaultProject::getOwner
	 */
	public function testGetOwner() {
		$expected = "";

		$actual = $this->object->getOwner();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::setOwner
	 */
	public function testSetOwner() {
		$expected = $this->owner;
		
		$this->object->setOwner($this->owner);

		$actual = $this->object->getOwner();
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @covers \Models\DefaultProject::getId
	 */
	public function testGetId() {
		$expected = 0;

		$actual = $this->object->getId();

		$this->assertSame($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::setId
	 */
	public function testSetId() {
		$expected = $this->id;

		$this->object->setId($expected);

		$actual = $this->object->getId();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\DefaultProject::getName
	 */
	public function testGetName() {
		$expected = "";

		$actual = $this->object->getName();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::setName
	 */
	public function testSetName() {
		$expected = $this->name;

		$this->object->setName($expected);

		$actual = $this->object->getName();
		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::getScripts
	 */
	public function testGetScripts_zeroInitialScripts() {
		$expecteds = array(
			"first_call" => array(),
			"second_call" => array(),
		);
		$actuals = array();
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(array('getInitialScripts'))
			->getMock();
		$this->object->expects($this->exactly(2))->method('getInitialScripts')->will($this->returnValue(array()));

		$actuals['first_call'] = $this->object->getScripts();
		$actuals['second_call'] = $this->object->getScripts();

		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::getScripts
	 */
	public function testGetScripts_manyInitialScripts() {
		$expected = array(1, 2, 3);
		$expecteds = array(
			$expected, 
			$expected, 
		);
		$actuals = array();
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(array('getInitialScripts'))
			->getMock();
		$this->object->expects($this->once())->method('getInitialScripts')->will($this->returnValue($expected));
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->getScripts();

		}
		$this->assertEquals($expecteds, $actuals);
	} 

	/**
	 * @covers \Models\DefaultProject::getFileTypes
	 */
	public function testGetFileTypes_manyInitialFileTypes() {
		$expected = array(1, 2, 3, new ArbitraryTextFileType());
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$initials = array(1, 2, 3);
		$this->object = $this->getMockBuilder("\Models\QIIMEProject")
			->disableOriginalConstructor()
			->setMethods(array("getInitialFileTypes"))
			->getMock();
		$this->object->expects($this->once())->method("getInitialFileTypes")->will($this->returnValue($initials));
		for($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->getFileTypes();

		}
		$this->assertEquals($expecteds, $actuals);
	} 

	/**
	 * @covers \Models\DefaultProject::getDatabase
	 */
	public function testGetDatabase() {
		$expected = $this->mockDatabase;

		$actual = $this->object->getDatabase();
		
		$this->assertSame($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::getOperatingSystem
	 */
	public function testGetOperatingSystem() {
		$expected = $this->mockOperatingSystem;

		$actual = $this->object->getOperatingSystem();
		
		$this->assertSame($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::getProjectDir
	 */
	public function testGetProjectDir_ownerNotReset_idNotReset() {
		$expecteds  = array(
			"u{$this->owner}/p{$this->id}",
			"u{$this->owner}/p{$this->id}",
		);
		$actuals = array();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getUserRoot'))
			->getMock();
		$mockDatabase->expects($this->once())->method('getUserRoot')->will($this->returnArgument(0));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$this->object->setOwner($this->owner);
		$this->object->setId($this->id);
		for($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->getProjectDir();

		}
		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::getProjectDir
	 */
	public function testGetProjectDir_lazyLoader_ownerNotReset_idReset() {
		$expecteds  = array(
			"u{$this->owner}/p{$this->id}",
			"u{$this->owner}/p{$this->newId}",
		);
		$actuals = array();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getUserRoot'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getUserRoot')->will($this->returnArgument(0));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$this->object->setOwner($this->owner);
		$this->object->setId($this->id);
		for($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->getProjectDir();

			$this->object->setId($this->newId);
		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\DefaultProject::getProjectDir
	 */
	public function testGetProjectDir_lazyLoader_ownerReset_idNotReset() {
		$expecteds  = array(
			"u{$this->owner}/p{$this->id}",
			"u{$this->newOwner}/p{$this->id}",
		);
		$actuals = array();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getUserRoot'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getUserRoot')->will($this->returnArgument(0));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$this->object->setOwner($this->owner);
		$this->object->setId($this->id);
		for($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->getProjectDir();

			$this->object->setOwner($this->newOwner);
		}
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\DefaultProject::getFileTypeFromHtmlId
	 */
	public function testGetFileTypeFromHtmlId_zeroFileTypes() {
		$expected = NULL;
		$id = "id";
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->setMethods(array("getHtmlId"))
			->getMockForAbstractClass();
		$mockFileType->expects($this->never())->method("getHtmlId")->will($this->returnValue($id));
		$this->object =  $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($this->mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getFileTypes"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getFileTypes")->will($this->returnValue(array()));

		$actual = $this->object->getFileTypeFromHtmlId($id);

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::getFileTypeFromHtmlId
	 */
	public function testGetFileTypeFromHtmlId_manyFileTypes_zeroMatches() {
		$expected = NULL;
		$id = "id";
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->setMethods(array("getHtmlId"))
			->getMockForAbstractClass();
		$mockFileType->expects($this->once())->method("getHtmlId")->will($this->returnValue($id));
		$this->object =  $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($this->mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getFileTypes"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getFileTypes")->will($this->returnValue(array($mockFileType)));

		$actual = $this->object->getFileTypeFromHtmlId("not_" . $id);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::getFileTypeFromHtmlId
	 */
	public function testGetFileTypeFromHtmlId_manyFileTypes_oneMatch() {
		$expectedId = "id";
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->setMethods(array("getHtmlId"))
			->getMockForAbstractClass();
		$expected = $mockFileType;
		$otherFileType = $this->getMockBuilder('\Models\FileType')
			->setMethods(array("getHtmlId"))
			->getMockForAbstractClass();
		$mockFileType->expects($this->once())->method("getHtmlId")->will($this->returnValue($expectedId));
		$otherFileType->expects($this->once())->method("getHtmlId")->will($this->returnValue("not_" . $expectedId));
		$this->object =  $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($this->mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getFileTypes"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getFileTypes")->will($this->returnValue(array($otherFileType, $mockFileType)));

		$actual = $this->object->getFileTypeFromHtmlId($expectedId);

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::getFileTypeFromHtmlId
	 */
	public function testGetFileTypeFromHtmlId_manyFileTypes_manyMatches() {
		$expectedId = "id";
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->setMethods(array("getHtmlId"))
			->getMockForAbstractClass();
		$expected = $mockFileType;
		$otherFileType = $this->getMockBuilder('\Models\FileType')
			->setMethods(array("getHtmlId"))
			->getMockForAbstractClass();
		$mockFileType->expects($this->once())->method("getHtmlId")->will($this->returnValue($expectedId));
		$otherFileType->expects($this->never())->method("getHtmlId")->will($this->returnValue($expectedId));
		$this->object =  $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($this->mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getFileTypes"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getFileTypes")->will($this->returnValue(array($mockFileType, $otherFileType)));

		$actual = $this->object->getFileTypeFromHtmlId($expectedId);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\DefaultProject::receiveDownloadedFile
	 */
	public function testReceiveDownloadedFile_databaseFails() {
		$expected = new \Exception("There was a problem storing your new file in the database");
		$actual = NULL;
		$fileType = $this->getMockBuilder('\Models\FileType')->getMockForAbstractClass();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(false));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		try {

			$this->object->receiveDownloadedFile("url", "fileName", $fileType);

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::receiveDownloadedFile
	 */
	public function testReceiveDownloadedFile_osFails() {
		$expectedMessage = "message";
		$expected = new OperatingSystemException($expectedMessage);
		$actual = NULL;
		$fileType = $this->getMockBuilder('\Models\FileType')
			->getMockForAbstractClass();
		$mockDatabase  = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("downloadFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("downloadFile")->will($this->throwException(new OperatingSystemException($expectedMessage)));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->receiveDownloadedFile("url", "fileName", $fileType);

		}
		catch(OperatingSystemException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::receiveDownloadedFile
	 */
	public function testReceiveDownloadedFile_nothingFails() {
		$expected = "console output";
		$fileType = $this->getMockBuilder('\Models\FileType')
			->getMockForAbstractClass();
		$mockDatabase  = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("downloadFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("downloadFile")->will($this->returnValue($expected));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->receiveDownloadedFile("url", "fileName", $fileType);

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::receiveDownloadedFile
	 */
	public function testReceiveUploadedFile_databaseFails() {
		$expected = new \Exception("Unable to create file in database");
		$actual = NULL;
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->getMockForAbstractClass();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("uploadFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(false));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->never())->method("uploadFile")->will($this->returnValue(true));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->receiveUploadedFile("givenName", "tmpName", -1, $mockFileType);

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::receiveDownloadedFile
	 */
	public function testReceiveUploadedFile_osFails() {
		$expectedMessage = "message";
		$expected = new OperatingSystemException($expectedMessage);
		$actual = NULL;
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->getMockForAbstractClass();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("uploadFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("uploadFile")->will($this->throwException(new OperatingSystemException($expectedMessage)));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->receiveUploadedFile("givenName", "tmpName", -1, $mockFileType);

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::receiveUploadedFile
	 */
	public function testReceiveUploadedFile_nothingFails() {
		$expected = NULL;
		$fileType = $this->getMockBuilder('\Models\FileType')
			->getMockForAbstractClass();
		$mockDatabase  = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem  = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("uploadFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("uploadFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->receiveUploadedFile("url", "fileName", $size = -1, $fileType);

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::deleteUploadedFile
	 */
	public function testDeleteUploadedFile_databaseFails() {
		$expected = new \Exception("Unable to remove record of file from the database");
		$actual = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("deleteFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(false));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->never())->method("deleteFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->deleteUploadedFile("fileName");

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::deleteUploadedFile
	 */
	public function testDeleteUploadedFile_osFails() {
		$expectedMessage = "message";
		$expected = new OperatingSystemException($expectedMessage);
		$actual = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("deleteFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("deleteFile")->will($this->throwException(new OperatingSystemException($expectedMessage)));;
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->deleteUploadedFile("fileName");

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::deleteUploadedFile
	 */
	public function testDeleteUploadedFile_nothingFails() {
		$expected = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("deleteFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("deleteFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->deleteUploadedFile("fileName");

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::unzipUploadedFile
	 */
	public function testUnzipUploadedFile_dbRemoveFails() {
		$expected = new \Exception("Unable to find/remove .zip file from the database");
		$actual = NULL;
		$mockDatabase  = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "createUploadedFile", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("unzipFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile");
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("createUploadedFile");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->never())->method("unzipFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->unzipUploadedFile("fileName");

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::unzipUploadedFile
	 */
	public function testUnzipUploadedFile_osFails() {
		$expectedMessage = "message";
		$expected = new OperatingSystemException($expectedMessage);
		$actual = NULL;
		$mockDatabase  = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "createUploadedFile", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("unzipFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("createUploadedFile");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("unzipFile")->will($this->throwException($expected));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->unzipUploadedFile("fileName");

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::unzipUploadedFile
	 */
	public function testUnzipUploadedFile_osSucceeds_returnsNothing() {
		$expected = NULL;
		$mockDatabase  = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "createUploadedFile", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("unzipFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("createUploadedFile");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("unzipFile")->will($this->returnValue(array()));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->unzipUploadedFile("fileName");

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::unzipUploadedFile
	 */
	public function testUnzipUploadedFile_dbCreateFails() {
		$expected = new \Exception("Unable to add unzipped file to database");
		$actual = NULL;
		$mockDatabase  = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "createUploadedFile", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("unzipFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->exactly(2))->method("createUploadedFile")->will($this->onConsecutiveCalls(true, false));
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("unzipFile")->will($this->returnValue(array("fileName1", "fileName2")));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->unzipUploadedFile("fileName");

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::unzipUploadedFile
	 */
	public function testUnzipUploadedFile_nothingFails() {
		$expected = NULL;
		$mockDatabase  = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "createUploadedFile", "executeAllRequests"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("unzipFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->exactly(2))->method("createUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockOperatingSystem->expects($this->once())->method("unzipFile")->will($this->returnValue(array("fileName1", "fileName2")));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->unzipUploadedFile("fileName");

		$this->assertEquals($expected, $actual);
	} 
	
	/**
	 * @covers \Models\DefaultProject::compressUploadedFile
	 */
	public function testCompressUploadedFile_databaseFails() {
		$expected = new \Exception("Unable to change file name, compression failed.");
		$actual = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("changeFileName"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("compressFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("changeFileName")->will($this->returnValue(false));
		$mockOperatingSystem->expects($this->once())->method("compressFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->compressUploadedFile("fileName");

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::compressUploadedFile
	 */
	public function testCompressUploadedFile_nothingFails() {
		$expected = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("changeFileName"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("compressFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("changeFileName")->will($this->returnValue(true));
		$mockOperatingSystem->expects($this->once())->method("compressFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->compressUploadedFile("fileName");

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::decompressUploadedFile
	 */
	public function testDecompressUploadedFile_databaseFails() {
		$expected = new \Exception("Unable to change file name, decompression failed.");
		$actual = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("changeFileName"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("decompressFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("changeFileName")->will($this->returnValue(false));
		$mockOperatingSystem->expects($this->once())->method("decompressFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->decompressUploadedFile("fileName");

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::decompressUploadedFile
	 */
	public function testDecompressUploadedFile_nothingFails() {
		$expected = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("changeFileName"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("decompressFile"))
			->getMock();
		$mockDatabase->expects($this->once())->method("changeFileName")->will($this->returnValue(true));
		$mockOperatingSystem->expects($this->once())->method("decompressFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->decompressUploadedFile("fileName");

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::deleteGeneratedFile
	 */
	public function testDeleteGeneratedFile() {
		$expected = NULL;
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('deleteFile'))
			->getMock();
		$mockOperatingSystem->expects($this->once())->method('deleteFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$actual = $this->object->deleteGeneratedFile("filename", "runId");

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::unzipGeneratedFile
	 */
	public function testUnzipGeneratedFile() {
		$expected = NULL;
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('unzipFile'))
			->getMock();
		$mockOperatingSystem->expects($this->once())->method('unzipFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$actual = $this->object->unzipGeneratedFile("filename", "runId");

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::compressGeneratedFile
	 */
	public function testCompressGeneratedFile() {
		$expected = NULL;
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('compressFile'))
			->getMock();
		$mockOperatingSystem->expects($this->once())->method('compressFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$actual = $this->object->compressGeneratedFile("filename", "runId");

		$this->asserTEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::decompressGeneratedFile
	 */
	public function testDecompressGeneratedFile() {
		$expected = NULL;
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('decompressFile'))
			->getMock();
		$mockOperatingSystem->expects($this->once())->method('decompressFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$actual = $this->object->decompressGeneratedFile("filename", "runId");

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllUploadedFiles_zeroUploadedFiles() {
		$expected = array();
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getAllUploadedFiles'))
			 ->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllUploadedFiles')->will($this->returnValue(array()));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		for($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllUploadedFiles();
			
		}
		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllUploadedFiles_manyUploadedFiles() {
		$expected = array(
			array("name" => "File1.ext", "type" => "map", "uploaded" => "true", "status" => "ready", "size" => "188"),	
			array("name" => "File2.ext", "type" => "sequence", "uploaded" => "true", "status" => "ready", "size" => "100000"),	
			array("name" => "File3.ext", "type" => "quality", "uploaded" => "true", "status" => "ready", "size" => "100000"),	
			array("name" => "File4.ext", "type" => "arbitrary_text", "uploaded" => "true", "status" => "ready", "size" => "100000"),	
		);
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$uploadedFiles = array(
			array("name" => "File1.ext", "file_type" => "map", "description" => "ready", "approx_size" => 188),	
			array("name" => "File2.ext", "file_type" => "sequence", "description" => "ready", "approx_size" => "100000"),	
			array("name" => "File3.ext", "file_type" => "quality", "description" => "ready", "approx_size" => "100000"),	
			array("name" => "File4.ext", "file_type" => "arbitrary_text", "description" => "ready", "approx_size" => "100000"),	
		);
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getAllUploadedFiles'))
			->getMock();
		$mockDatabase->expects($this->once())->method('getAllUploadedFiles')->will($this->returnValue($uploadedFiles));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllUploadedFiles();

		}
		$this->assertEquals($expecteds, $actuals);
	} 

	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllUploadedFiles_lazyLoader_recieveUploadedFilesCalled() {
		$expected = array(
			array("name" => "File1.ext", "type" => "map", "uploaded" => "true", "status" => "ready", "size" => "188"),	
		);
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$uploadedFiles = array(
			array("name" => "File1.ext", "file_type" => "map", "description" => "ready", "approx_size" => 188),	
		);
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->getMockForAbstractClass();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getAllUploadedFiles', 'createUploadedFile', 'startTakingRequests', 'executeAllRequests'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllUploadedFiles')->will($this->returnValue($uploadedFiles));
		$mockDatabase->expects($this->exactly(2))->method('createUploadedFile')->will($this->returnValue(true));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllUploadedFiles();

			$this->object->receiveUploadedFile("url", "fileName", 1088, $mockFileType);
		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllUploadedFiles_lazyLoader_recieveDownloadedFilesCalled() {
		$expected = array(
			array("name" => "File1.ext", "type" => "map", "uploaded" => "true", "status" => "ready", "size" => "188"),	
		);
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$uploadedFiles = array(
			array("name" => "File1.ext", "file_type" => "map", "description" => "ready", "approx_size" => 188),	
		);
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->getMockForAbstractClass();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getAllUploadedFiles', 'createUploadedFile', 'startTakingRequests', 'executeAllRequests'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllUploadedFiles')->will($this->returnValue($uploadedFiles));
		$mockDatabase->expects($this->exactly(2))->method('createUploadedFile')->will($this->returnValue(true));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllUploadedFiles();

			$this->object->receiveDownloadedFile("url", "fileName", $mockFileType);
		}
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\DefaultProject::getPastScriptRuns
	 */
	public function testGetPastScriptRuns_zeroRuns() {
		$expected = array();
		$expecteds = array(
			$expected,
			$expected,
		);
		$actual = array();
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getAllRuns'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue(array()));
		$this->object = $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("attemptGetDirContents", "getProjectDir"))
			->getMockForAbstractClass();
		$this->object->expects($this->never())->method("attemptGetDirContents")->will($this->returnValue(array()));
		$this->object->expects($this->never())->method("getProjectDir")->will($this->returnValue(""));
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->getPastScriptRuns();

		}
		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::getPastScriptRuns
	 */
	public function testGetPastScriptRuns_manyRuns() {
		$expectedGeneratedFiles = array("output.txt", "env.txt", "error_log.txt", "gen.csv");
		$expected = array(
			array("id" => "1", "name" => "validate_mapping_file.py", "input" => "validate_mapping_file.py -i 'value'",
				"file_names" => $expectedGeneratedFiles, "is_finished" => true, "is_deleted" => false, "pid" => "-1"),
			array("id" => "2", "name" => "make_otu_table.py", "input" => "make_otu_table.py --input='value'",
				"file_names" => $expectedGeneratedFiles, "is_finished" => false, "is_deleted" => false, "pid" => "1000"),
		);
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$runsInDatabase = array(
			array("id" => "1", "script_name" => "validate_mapping_file.py",
				"script_string" => "validate_mapping_file.py -i 'value'", "run_status" => "-1", "deleted" => false),
			array("id" => "2", "script_name" => "make_otu_table.py",
				"script_string" => "make_otu_table.py --input='value'", "run_status" => "1000", "deleted" => false),
		);
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getAllRuns'))
			->getMock();
		$mockDatabase->expects($this->once())->method('getAllRuns')->will($this->returnValue($runsInDatabase));
		$this->object = $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("attemptGetDirContents", "getProjectDir"))
			->getMockForAbstractClass();
		$this->object->expects($this->exactly(2))->method("attemptGetDirContents")->will($this->returnValue($expectedGeneratedFiles));
		$this->object->expects($this->exactly(2))->method("getProjectDir")->will($this->returnValue(""));
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->getPastScriptRuns();

		}
		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testGetPastScriptRuns_lazyLoader_runScriptsCalled() {
		$expectedGeneratedFiles = array("output.txt", "env.txt", "error_log.txt", "gen.csv");
		$expected = array(
			array("id" => "1", "name" => "validate_mapping_file.py", "input" => "validate_mapping_file.py -i 'value'",
				"file_names" => $expectedGeneratedFiles, "is_finished" => true, "is_deleted" => false, "pid" => "-1"),
		);
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$runsInDatabase = array(
			array("id" => "1", "script_name" => "validate_mapping_file.py",
				"script_string" => "validate_mapping_file.py -i 'value'", "run_status" => "-1", "deleted" => false),
		);
		$scriptName = "script_name.py";
		$input = array("script" => $scriptName);
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("acceptInput", "renderCommand"))
			->getMockForAbstractClass();
		$scripts = array($scriptName => $mockScript);
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('getAllRuns', 'createRun', 'giveRunPid', 'startTakingRequests', 'executeAllRequests'))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('runScript'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue($runsInDatabase));
		$mockDatabase->expects($this->exactly(2))->method('createRun')->will($this->returnValue(true));
		$mockDatabase->expects($this->exactly(2))->method('giveRunPid')->will($this->returnValue(true));
		$this->object = $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($mockDatabase, $mockOperatingSystem))
			->setMethods(array("attemptGetDirContents", "getProjectDir", "getScripts"))
			->getMockForAbstractClass();
		$this->object->expects($this->exactly(2))->method("attemptGetDirContents")->will($this->returnValue($expectedGeneratedFiles));
		$this->object->expects($this->exactly(2))->method("getProjectDir")->will($this->returnValue(""));
		$this->object->expects($this->exactly(2))->method("getScripts")->will($this->returnValue($scripts));
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->getPastScriptRuns();

			$this->object->runScript($input);
		}
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_noRuns() {
		$expected = array();
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$runs = array();
		$mockDatabase = $this->getMockBuilder("\Database\PDODatabase") ->disableOriginalConstructor()
			->setMethods(array('getAllRuns'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue($runs));
		$this->object = $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getProjectDir", "attemptGetDirContents"))
			->getMockForAbstractClass();
		$this->object->expects($this->never())->method("getProjectDir");
		$this->object->expects($this->never())->method("attemptGetDirContents");
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllGeneratedFiles();

		}
		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_oneRun_noFiles() {
		$expected = array();
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$runs = array(
			array("id" => 1),
		);
		$mockDatabase = $this->getMockBuilder("\Database\PDODatabase")->disableOriginalConstructor()
			->setMethods(array('getAllRuns'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue($runs));
		$this->object = $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getProjectDir", "attemptGetDirContents"))
			->getMockForAbstractClass();
		$this->object->expects($this->exactly(2))->method("getProjectDir");
		$this->object->expects($this->exactly(2))->method("attemptGetDirContents")->will($this->returnValue(array()));
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllGeneratedFiles();

		}
		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_oneRun_manyFiles() {
		$expected = array(
			array("name" => "file1.txt", "run_id" => 1),
			array("name" => "file2.txt", "run_id" => 1),
		);
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$runs = array(
			array("id" => 1),
		);
		$mockDatabase = $this->getMockBuilder("\Database\PDODatabase")->disableOriginalConstructor()
			->setMethods(array('getAllRuns'))
			->getMock();
		$mockDatabase->expects($this->once())->method('getAllRuns')->will($this->returnValue($runs));
		$this->object = $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getProjectDir", "attemptGetDirContents"))
			->getMockForAbstractClass();
		$this->object->expects($this->once())->method("getProjectDir");
		$this->object->expects($this->once())->method("attemptGetDirContents")->will($this->returnValue(array("file1.txt", "file2.txt")));
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllGeneratedFiles();

		}
		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_manyRuns_noFiles() {
		$expected = array();
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$runs = array(
			array("id" => 1),
			array("id" => 2),
		);
		$mockDatabase = $this->getMockBuilder("\Database\PDODatabase")->disableOriginalConstructor()
			->setMethods(array('getAllRuns'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue($runs));
		$this->object = $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getProjectDir", "attemptGetDirContents"))
			->getMockForAbstractClass();
		$this->object->expects($this->exactly(2*2))->method("getProjectDir");
		$this->object->expects($this->exactly(2*2))->method("attemptGetDirContents")->will($this->returnValue(array()));
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllGeneratedFiles();

		}
		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_manyRuns_manyFiles() {
		$expected = array(
			array("name" => "file1.txt", "run_id" => 1),
			array("name" => "file2.txt", "run_id" => 1),
			array("name" => "file1.txt", "run_id" => 2),
			array("name" => "file2.txt", "run_id" => 2),
		);
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$runs = array(
			array("id" => 1),
			array("id" => 2),
		);
		$mockDatabase = $this->getMockBuilder("\Database\PDODatabase")->disableOriginalConstructor()
			->setMethods(array('getAllRuns'))
			->getMock();
		$mockDatabase->expects($this->once())->method('getAllRuns')->will($this->returnValue($runs));
		$this->object = $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getProjectDir", "attemptGetDirContents"))
			->getMockForAbstractClass();
		$this->object->expects($this->exactly(2))->method("getProjectDir");
		$this->object->expects($this->exactly(2))->method("attemptGetDirContents")->will($this->returnValue(array("file1.txt", "file2.txt")));
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllGeneratedFiles();

		}
		$this->assertEquals($expecteds, $actuals);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllGeneratedFiles_lazyLoader_runScriptIsCalled() {
		$expected = array(
			array("name" => "file1.txt", "run_id" => 1),
		);
		$expecteds = array(
			$expected,
			$expected,
		);
		$actuals = array();
		$runs = array(
			array("id" => 1),
		);
		$scriptName = "script_name.py";
		$input = array("script" => $scriptName);
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array("acceptInput", "renderCommand"))
			->getMockForAbstractClass();
		$scripts = array($scriptName => $mockScript);
		$mockDatabase = $this->getMockBuilder("\Database\PDODatabase")->disableOriginalConstructor()
			->setMethods(array('getAllRuns', 'startTakingRequests', 'createRun', 'giveRunPid', 'executeAllRequests'))
			->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue($runs));
		$mockDatabase->expects($this->exactly(2))->method('createRun')->will($this->returnValue(true));
		$mockDatabase->expects($this->exactly(2))->method('giveRunPid')->will($this->returnValue(true));
		$this->object = $this->getMockBuilder('\Models\DefaultProject')
			->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getProjectDir", "attemptGetDirContents", "getScripts"))
			->getMockForAbstractClass();
		$this->object->expects($this->exactly(2))->method("getProjectDir");
		$this->object->expects($this->exactly(2))->method("attemptGetDirContents")->will($this->returnValue(array("file1.txt")));
		$this->object->expects($this->exactly(2))->method("getScripts")->will($this->returnValue($scripts));
		for ($i = 0; $i < 2; $i++) {

			$actuals[] = $this->object->retrieveAllGeneratedFiles();

			$this->object->runScript($input);
		}
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers \Models\DefaultProject::attemptGetDirContents
	 */
	public function testAttemptGetDirContents_osFails() {
		$expected = array();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("getDirContents"))
			->getMock();
		$mockOperatingSystem->expects($this->once())->method("getDirContents")->will($this->throwException(new OperatingSystemException()));
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$actual = $this->object->attemptGetDirContents("dirName");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::attemptGetDirContents
	 */
	public function testAttemptGetDirContents_nothingFails_osReturnsNothing() {
		$expected = array();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("getDirContents"))
			->getMock();
		$mockOperatingSystem->expects($this->once())->method("getDirContents")->will($this->returnValue($expected));
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$actual = $this->object->attemptGetDirContents("dirName");

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::attemptGetDirContents
	 */
	public function testAttemptGetDirContents_nothingFails() {
		$expected = array("file1.txt", "file2.txt");
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->setMethods(array("getDirContents"))
			->getMock();
		$mockOperatingSystem->expects($this->once())->method("getDirContents")->will($this->returnValue($expected));
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$actual = $this->object->attemptGetDirContents("dirName");

		$this->assertEquals($expected, $actual);
	}
	
	/**
	 * @covers \Models\DefaultProject::runScript
	 */
	public function testRunScript_invalidScriptId() {
		$expected = new \Exception("Unable to find script: 4");
		$actual = NULL;
		$input = array('script' => 4);
		$mockHelper = $this->getMockBuilder('\Utils\Helper')
			->setMethods(array("htmlentities"))
			->getMock();
		$mockHelper->expects($this->once())->method("htmlentities")->will($this->returnArgument(0));
		\Utils\Helper::setDefaultHelper($mockHelper);
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->setConstructorArgs(array($this->mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getScripts"))
			->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue(array(1, 2, 3)));
		try {

			$this->object->runScript($input);
		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		\Utils\Helper::setDefaultHelper(NULL);
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 */
	public function testRunScript_invalidScriptInput() {
		$expected = new ScriptException("message");
		$actual = NULL;
		$input = array('script' => 1);
		$mockScript = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile')
			->disableOriginalConstructor()
			->setMethods(array('acceptInput'))
			->getMock();
		$scripts = array(1 => $mockScript);
		$mockScript->expects($this->once())->method('acceptInput')->will($this->throwException($expected));
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->setConstructorArgs(array($this->mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getScripts"))
			->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		try {

			$this->object->runScript($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 */
	public function testRunScript_dbCreateRunFails() {
		$expected = new \Exception("Able to validate script input-<br/>However, we were unable to save the run in the database.");
		$actual = NULL;
		$input = array('script' => 1);
		$mockScript = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile')
			->disableOriginalConstructor()
			->setMethods(array('acceptInput', 'renderCommand'))
			->getMock();
		$scripts = array(1 => $mockScript);
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('startTakingRequests', 'createRun'))
			->getMock();
		$mockScript->expects($this->once())->method('acceptInput');
		$mockScript->expects($this->once())->method('renderCommand');
		$mockDatabase->expects($this->once())->method('startTakingRequests');
		$mockDatabase->expects($this->once())->method('createRun')->will($this->returnValue(false));
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem))
			->setMethods(array("getScripts"))
			->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		try {

			$this->object->runScript($input);

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 */
	public function testRunScript_osRunScriptFails() {
		$expected = new \Exception("message");
		$actual = NULL;
		$input = array('script' => 1);
		$mockScript = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile')
			->disableOriginalConstructor()
			->setMethods(array('acceptInput', 'renderCommand'))
			->getMock();
		$scripts = array(1 => $mockScript);
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('startTakingRequests', 'createRun', 'forgetAllRequests'))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('runScript'))
			->getMock();
		$mockScript->expects($this->once())->method('acceptInput');
		$mockScript->expects($this->once())->method('renderCommand');
		$mockDatabase->expects($this->once())->method('startTakingRequests');
		$mockDatabase->expects($this->once())->method('createRun')->will($this->returnValue(1));
		$mockDatabase->expects($this->once())->method('forgetAllRequests');
		$mockOperatingSystem->expects($this->once())->method("runScript")->will($this->throwException($expected));
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->setConstructorArgs(array($mockDatabase, $mockOperatingSystem))
			->setMethods(array("getScripts"))
			->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		try {

			$this->object->runScript($input);

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 */
	public function testRunScript_dbGivRunPidFails() {
		$expected = new \Exception("Able to validate script input-<br/>However, we were unable to save the run in the database.");
		$actual = NULL;
		$input = array('script' => 1);
		$mockScript = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile')
			->disableOriginalConstructor()
			->setMethods(array('acceptInput', 'renderCommand'))
			->getMock();
		$scripts = array(1 => $mockScript);
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('startTakingRequests', 'createRun', 'forgetAllRequests', 'giveRunPid'))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('runScript'))
			->getMock();
		$mockScript->expects($this->once())->method('acceptInput');
		$mockScript->expects($this->once())->method('renderCommand');
		$mockDatabase->expects($this->once())->method('startTakingRequests');
		$mockDatabase->expects($this->once())->method('createRun')->will($this->returnValue(1));
		$mockDatabase->expects($this->once())->method('forgetAllRequests');
		$mockDatabase->expects($this->once())->method('giveRunPid')->will($this->returnValue(false));
		$mockOperatingSystem->expects($this->once())->method("runScript");
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->setConstructorArgs(array($mockDatabase, $mockOperatingSystem))
			->setMethods(array("getScripts"))
			->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		try {

			$this->object->runScript($input);

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 */
	public function testRunScript_nothingFails() {
		$expected = "Able to validate script input-<br/>Script was started successfully";
		$input = array('script' => 1);
		$mockScript = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile')
			->disableOriginalConstructor()
			->setMethods(array('acceptInput', 'renderCommand'))
			->getMock();
		$scripts = array(1 => $mockScript);
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array('startTakingRequests', 'createRun', 'forgetAllRequests', 'giveRunPid', 'executeAllRequests'))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('runScript'))
			->getMock();
		$mockScript->expects($this->once())->method('acceptInput');
		$mockScript->expects($this->once())->method('renderCommand');
		$mockDatabase->expects($this->once())->method('startTakingRequests');
		$mockDatabase->expects($this->once())->method('createRun')->will($this->returnValue(1));
		$mockDatabase->expects($this->never())->method('forgetAllRequests');
		$mockDatabase->expects($this->once())->method('giveRunPid')->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method('executeAllRequests');
		$mockOperatingSystem->expects($this->once())->method("runScript");
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->setConstructorArgs(array($mockDatabase, $mockOperatingSystem))
			->setMethods(array("getScripts"))
			->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));

		$actual = $this->object->runScript($input);

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::renderOverview
	 */
	public function testRenderOverview_zeroFormattedScripts() {
		$expected = "<div id=\"project_overview\">\n</div>\n";
		$initialFormattedScripts = array();
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(array('getFormattedScripts'))
			->getMock();
		$this->object->expects($this->once())->method('getFormattedScripts')->will($this->returnValue($initialFormattedScripts));
		
		$actual = $this->object->renderOverview();

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::renderOverview
	 */
	public function testRenderOverview_oneFormattedScript() {
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array('getHtmlId', 'getScriptName', 'getScriptTitle'))
			->getMock();
		$mockScript->expects($this->any())->method('getHtmlId')->will($this->returnValue('id'));
		$mockScript->expects($this->any())->method('getScriptName')->will($this->returnValue('name'));
		$mockScript->expects($this->any())->method('getScriptTitle')->will($this->returnValue('title'));
		$mockScriptString = "<span><a class=\"button\" onclick=\"displayHideables('{$mockScript->getHtmlId()}');\" title=\"{$mockScript->getScriptName()}\">{$mockScript->getScriptTitle()}</a></span>";
		$expected = "<div id=\"project_overview\">\n<div><span>cat1</span>{$mockScriptString}</div>\n</div>\n";
		$initialFormattedScripts = array(
			'cat1' => array($mockScript),
		);
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(array('getFormattedScripts'))
			->getMock();
		$this->object->expects($this->once())->method('getFormattedScripts')->will($this->returnValue($initialFormattedScripts));
		
		$actual = $this->object->renderOverview();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::renderOverview
	 */
	public function testRenderOverview_manyFormattedScripts_oneCategory() {
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array('getHtmlId', 'getScriptName', 'getScriptTitle'))
			->getMock();
		$mockScript->expects($this->any())->method('getHtmlId')->will($this->returnValue('id'));
		$mockScript->expects($this->any())->method('getScriptName')->will($this->returnValue('name'));
		$mockScript->expects($this->any())->method('getScriptTitle')->will($this->returnValue('title'));
		$mockScriptString = "<span><a class=\"button\" onclick=\"displayHideables('{$mockScript->getHtmlId()}');\" title=\"{$mockScript->getScriptName()}\">{$mockScript->getScriptTitle()}</a></span>";
		$expected = "<div id=\"project_overview\">\n<div><span>cat1</span>{$mockScriptString}{$mockScriptString}</div>\n</div>\n";
		$initialFormattedScripts = array(
			'cat1' => array($mockScript, $mockScript),
		);
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(array('getFormattedScripts'))
			->getMock();
		$this->object->expects($this->once())->method('getFormattedScripts')->will($this->returnValue($initialFormattedScripts));
		
		$actual = $this->object->renderOverview();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::renderOverview
	 */
	public function testRenderOverview_manyFormattedScripts_manyCategories() {
		$mockScript = $this->getMockBuilder('\Models\Scripts\DefaultScript')
			->disableOriginalConstructor()
			->setMethods(array('getHtmlId', 'getScriptName', 'getScriptTitle'))
			->getMock();
		$mockScript->expects($this->any())->method('getHtmlId')->will($this->returnValue('id'));
		$mockScript->expects($this->any())->method('getScriptName')->will($this->returnValue('name'));
		$mockScript->expects($this->any())->method('getScriptTitle')->will($this->returnValue('title'));
		$mockScriptString = "<span><a class=\"button\" onclick=\"displayHideables('{$mockScript->getHtmlId()}');\" title=\"{$mockScript->getScriptName()}\">{$mockScript->getScriptTitle()}</a></span>";
		$expected = "<div id=\"project_overview\">\n<div><span>cat1</span>{$mockScriptString}</div>\n<div><span>cat2</span>{$mockScriptString}{$mockScriptString}</div>\n</div>\n";
		$initialFormattedScripts = array(
			'cat1' => array($mockScript),
			'cat2' => array($mockScript, $mockScript),
		);
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(array('getFormattedScripts'))
			->getMock();
		$this->object->expects($this->once())->method('getFormattedScripts')->will($this->returnValue($initialFormattedScripts));
		
		$actual = $this->object->renderOverview();

		$this->assertEquals($expected, $actual);
	}
}
