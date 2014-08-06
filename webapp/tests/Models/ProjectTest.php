<?php

namespace Models;

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
			"first_call" => $expected, 
			"second_call" => $expected, 
		);
		$actuals = array();
		$this->object = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(array('getInitialScripts'))
			->getMock();
		$this->object->expects($this->once())->method('getInitialScripts')->will($this->returnValue($expected));

		$actuals['first_call'] = $this->object->getScripts();
		$actuals['second_call'] = $this->object->getScripts();

		$this->assertEquals($expecteds, $actuals);
	} 

	/**
	 * @covers \Models\DefaultProject::getFileTypes
	 */
	public function testGetFileTypes_manyInitialFileTypes() {
		$expected = array(1, 2, 3, new ArbitraryTextFileType());
		$expecteds = array(
			"first_call" => $expected,
			"second_call" => $expected,
		);
		$actuals = array();
		$initials = array(1, 2, 3);
		$this->object = $this->getMockBuilder("\Models\QIIMEProject")
			->disableOriginalConstructor()
			->setMethods(array("getInitialFileTypes"))
			->getMock();
		$this->object->expects($this->once())->method("getInitialFileTypes")->will($this->returnValue($initials));

		$actuals['first_call'] = $this->object->getFileTypes();
		$actuals['second_call'] = $this->object->getFileTypes();

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
	public function testGetProjectDir_ownerNotReset_idReset() {
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
	public function testGetProjectDir_ownerReset_idNotReset() {
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
	public function testReceiveUploadedFile_operatingSystemFails() {
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
	public function testDeleteUploadedFile_dbFails() {
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
	 * @expectedException \Exception
	 */
	public function testCompressUploadedFile_dbFails() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("changeFileName"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("changeFileName");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("compressFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("compressFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->compressUploadedFile("fileName");

		$this->fail("compressUploadedFile should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::compressUploadedFile
	 */
	public function testCompressUploadedFile_nothingFails() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("changeFileName"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("changeFileName")->will($this->returnValue(true));
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("compressFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("compressFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->compressUploadedFile("fileName");
	} 
	/**
	 * @covers \Models\DefaultProject::decompressUploadedFile
	 * @expectedException \Exception
	 */
	public function testDecompressUploadedFile_dbFails() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("changeFileName"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("changeFileName");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("decompressFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("decompressFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->decompressUploadedFile("fileName");

		$this->fail("decompressUploadedFile should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::decompressUploadedFile
	 */
	public function testDecompressUploadedFile_nothingFails() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("changeFileName"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("changeFileName")->will($this->returnValue(true));
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("decompressFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("decompressFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->decompressUploadedFile("fileName");
	} 

	/**
	 * @covers \Models\DefaultProject::deleteGeneratedFile
	 */
	public function testDeleteGeneratedFile() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('deleteFile'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method('deleteFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$this->object->deleteGeneratedFile("filename", "runId");
	} 
	/**
	 * @covers \Models\DefaultProject::unzipGeneratedFile
	 */
	public function testUnzipGeneratedFile() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('unzipFile'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method('unzipFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$this->object->unzipGeneratedFile("filename", "runId");
	} 
	/**
	 * @covers \Models\DefaultProject::compressGeneratedFile
	 */
	public function testCompressGeneratedFile() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('compressFile'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method('compressFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$this->object->compressGeneratedFile("filename", "runId");
	} 
	/**
	 * @covers \Models\DefaultProject::decompressGeneratedFile
	 */
	public function testDecompressGeneratedFile() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('decompressFile'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method('decompressFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$this->object->decompressGeneratedFile("filename", "runId");
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllUploadedFiles_zeroUploadedFiles() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getAllUploadedFiles'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllUploadedFiles')->will($this->returnValue(array()));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->retrieveAllUploadedFiles();
		$actual = $this->object->retrieveAllUploadedFiles();

		$this->assertEmpty($actual);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllUploadedFiles_manyUploadedFiles() {
		$this->markTestIncomplete();
		$uploadedFiles = array(
			array("name" => "File1.ext", "file_type" => "map", "description" => "ready", "approx_size" => 188),	
			array("name" => "File2.ext", "file_type" => "sequence", "description" => "ready", "approx_size" => "100000"),	
			array("name" => "File3.ext", "file_type" => "quality", "description" => "ready", "approx_size" => "100000"),	
			array("name" => "File4.ext", "file_type" => "arbitrary_text", "description" => "ready", "approx_size" => "100000"),	
		);
		// every type, size, and status (name variations too)
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getAllUploadedFiles'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method('getAllUploadedFiles')->will($this->returnValue($uploadedFiles));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$expected = array(
			array("name" => "File1.ext", "type" => "map", "uploaded" => "true", "status" => "ready", "size" => "188"),	
			array("name" => "File2.ext", "type" => "sequence", "uploaded" => "true", "status" => "ready", "size" => "100000"),	
			array("name" => "File3.ext", "type" => "quality", "uploaded" => "true", "status" => "ready", "size" => "100000"),	
			array("name" => "File4.ext", "type" => "arbitrary_text", "uploaded" => "true", "status" => "ready", "size" => "100000"),	
		);

		$actual = $this->object->retrieveAllUploadedFiles();
		$actual = $this->object->retrieveAllUploadedFiles();

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllUploadedFiles_lazyLoadResetters() {
		$this->markTestIncomplete();
	}

	/**
	 * @covers \Models\DefaultProject::getPastScriptRuns
	 */
	public function testGetPastScriptRuns_zeroRuns() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getAllRuns'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue(array()));
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getDirContents'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->never())->method('getDirContents');
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);

		$actual = $this->object->getPastScriptRuns();
		$actual = $this->object->getPastScriptRuns();

		$this->assertEmpty($actual);
	} 
	/**
	 * @covers \Models\DefaultProject::getPastScriptRuns
	 */
	public function testGetPastScriptRuns_manyRuns() {
		$this->markTestIncomplete();
		$runsInDatabase = array(
			array("id" => "1", "script_name" => "validate_mapping_file.py",
				"script_string" => "validate_mapping_file.py -i 'value'", "run_status" => "-1", "deleted" => false),
			array("id" => "2", "script_name" => "make_otu_table.py",
				"script_string" => "make_otu_table.py --input='value'", "run_status" => "1000", "deleted" => false),
		);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getAllRuns', 'getUserRoot'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method('getAllRuns')->will($this->returnValue($runsInDatabase));
		$mockDatabase->expects($this->once())->method('getUserRoot')->will($this->returnValue(1));
		$generatedFiles = array("output.txt", "env.txt", "error_log.txt", "gen.csv");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getDirContents'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->exactly(2))->method('getDirContents')->will($this->returnValue($generatedFiles));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		$expected = array(
			array("id" => "1", "name" => "validate_mapping_file.py", "input" => "validate_mapping_file.py -i 'value'",
				"file_names" => $generatedFiles, "is_finished" => true, "is_deleted" => false, "pid" => "-1"),
			array("id" => "2", "name" => "make_otu_table.py", "input" => "make_otu_table.py --input='value'",
				"file_names" => $generatedFiles, "is_finished" => false, "is_deleted" => false, "pid" => "1000"),
		);

		$actual = $this->object->getPastScriptRuns();
		$actual = $this->object->getPastScriptRuns();

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testGetPastScriptRuns_lazyLoadResetters() {
		$this->markTestIncomplete();
	}

	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_noRuns() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder("\Database\PDODatabase");
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getAllRuns'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue(array()));
		$mockBuilder = $this->getMockBuilder("\Models\MacOperatingSystem");
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getDirContents'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->never())->method('getDirContents');
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->retrieveAllGeneratedFiles();
		$actual = $this->object->retrieveAllGeneratedFiles();

		$this->assertEmpty($actual);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_oneRun_noFiles() {
		$this->markTestIncomplete();
		$runsInDatabase = array(
			array("id" => 1),
		);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getAllRuns', 'getUserRoot'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue($runsInDatabase));
		$mockDatabase->expects($this->once())->method('getUserRoot')->will($this->returnValue(1));
		$generatedFiles = array();
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getDirContents'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->exactly(2))->method('getDirContents')->will($this->returnValue($generatedFiles));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		$expected = array();
		foreach ($generatedFiles as $file) {
			$expected[] = array("name" => $file, "run_id" => 1);
		}

		$actual = $this->object->retrieveAllGeneratedFiles();
		$actual = $this->object->retrieveAllGeneratedFiles();

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_oneRun_manyFiles() {
		$this->markTestIncomplete();
		$runsInDatabase = array(
			array("id" => 1),
		);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getAllRuns', 'getUserRoot'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method('getAllRuns')->will($this->returnValue($runsInDatabase));
		$mockDatabase->expects($this->once())->method('getUserRoot')->will($this->returnValue(1));
		$generatedFiles = array("output.txt", "env.txt", "error_log.txt", "gen.csv");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getDirContents'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->exactly(1))->method('getDirContents')->will($this->returnValue($generatedFiles));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		$expected = array();
		foreach ($generatedFiles as $file) {
			$expected[] = array("name" => $file, "run_id" => 1);
		}

		$actual = $this->object->retrieveAllGeneratedFiles();
		$actual = $this->object->retrieveAllGeneratedFiles();

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_manyRuns_noFiles() {
		$this->markTestIncomplete();
		$runsInDatabase = array(
			array("id" => 1),
			array("id" => 2),
		);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getAllRuns', 'getUserRoot'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getAllRuns')->will($this->returnValue($runsInDatabase));
		$mockDatabase->expects($this->once())->method('getUserRoot')->will($this->returnValue(1));
		$generatedFiles = array();
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getDirContents'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->exactly(4))->method('getDirContents')->will($this->returnValue($generatedFiles));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->retrieveAllGeneratedFiles();
		$actual = $this->object->retrieveAllGeneratedFiles();

		$this->assertEmpty($actual);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_manyRuns_manyFiles() {
		$this->markTestIncomplete();
		$runsInDatabase = array(
			array("id" => 1),
			array("id" => 2),
		);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getAllRuns', 'getUserRoot'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->exactly(1))->method('getAllRuns')->will($this->returnValue($runsInDatabase));
		$mockDatabase->expects($this->once())->method('getUserRoot')->will($this->returnValue(1));
		$generatedFiles = array("output.txt", "env.txt", "error_log.txt", "gen.csv");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getDirContents'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->exactly(2))->method('getDirContents')->will($this->returnValue($generatedFiles));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		$expected = array();
		foreach ($generatedFiles as $file) {
			$expected[] = array("name" => $file, "run_id" => 1);
		}
		foreach ($generatedFiles as $file) {
			$expected[] = array("name" => $file, "run_id" => 2);
		}

		$actual = $this->object->retrieveAllGeneratedFiles();
		$actual = $this->object->retrieveAllGeneratedFiles();

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllGeneratedFiles_lazyLoadResetters() {
		$this->markTestIncomplete();
	}

	/**
	 * @covers \Models\DefaultProject::attemptGetDirContents
	 */
	public function testAttemptGetDirContents() {
		$this->markTestIncomplete();
	}
	
	/**
	 * @covers \Models\DefaultProject::runScript
	 * @expectedException \Exception
	 */
	public function testRunScript_invalidScriptId() {
		$this->markTestIncomplete();
		$input = array('script' => 4);
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEProject');
		$mockBuilder->setConstructorArgs($this->mockDatabase, $this->mockOperatingSystem);
		$mockBuilder->setMethods(array("getScripts"));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue(array(1, 2, 3)));

		$this->object->runScript($input);

		$this->fail("runScript should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 * @expectedException \Models\Scripts\ScriptException
	 */
	public function testRunScript_invalidScriptInput() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('acceptInput'));
		$mockScript = $mockBuilder->getMock();
		$mockScript->expects($this->once())->method('acceptInput')->will($this->throwException(new \Models\Scripts\ScriptException()));
		$scripts = array(1 => $mockScript);
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEProject');
		$mockBuilder->setConstructorArgs(array($this->mockDatabase, $this->mockOperatingSystem));
		$mockBuilder->setMethods(array("getScripts"));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		$input = array('script' => 1);

		$this->object->runScript($input);

		$this->fail("runScript should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 * @expectedException \Exception
	 */
	public function testRunScript_dbCreateRunFails() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('acceptInput', 'renderCommand'));
		$mockScript = $mockBuilder->getMock();
		$mockScript->expects($this->once())->method('acceptInput');
		$mockScript->expects($this->once())->method('renderCommand');
		$scripts = array(1 => $mockScript);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('startTakingRequests', 'createRun'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method('startTakingRequests');
		$mockDatabase->expects($this->once())->method('createRun')->will($this->returnValue(false));
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEProject');
		$mockBuilder->setConstructorArgs(array($mockDatabase, $this->mockOperatingSystem));
		$mockBuilder->setMethods(array("getScripts"));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		$input = array('script' => 1);

		$this->object->runScript($input);

		$this->fail("runScript should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 * @expectedException \Exception
	 */
	public function testRunScript_osRunScriptFails() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('acceptInput', 'renderCommand'));
		$mockScript = $mockBuilder->getMock();
		$mockScript->expects($this->once())->method('acceptInput');
		$mockScript->expects($this->once())->method('renderCommand');
		$scripts = array(1 => $mockScript);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('startTakingRequests', 'createRun', 'forgetAllRequests'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method('startTakingRequests');
		$mockDatabase->expects($this->once())->method('createRun')->will($this->returnValue(1));
		$mockDatabase->expects($this->once())->method('forgetAllRequests');
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('runScript'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("runScript")->will($this->throwException(new \Exception()));
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEProject');
		$mockBuilder->setConstructorArgs(array($mockDatabase, $mockOperatingSystem));
		$mockBuilder->setMethods(array("getScripts"));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		$input = array('script' => 1);

		$this->object->runScript($input);

		$this->fail("runScript should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 * @expectedException \Exception
	 */
	public function testRunScript_dbGivRunPidFails() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('acceptInput', 'renderCommand'));
		$mockScript = $mockBuilder->getMock();
		$mockScript->expects($this->once())->method('acceptInput');
		$mockScript->expects($this->once())->method('renderCommand');
		$scripts = array(1 => $mockScript);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('startTakingRequests', 'createRun', 'forgetAllRequests', 'giveRunPid'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method('startTakingRequests');
		$mockDatabase->expects($this->once())->method('createRun')->will($this->returnValue(1));
		$mockDatabase->expects($this->once())->method('forgetAllRequests');
		$mockDatabase->expects($this->once())->method('giveRunPid')->will($this->returnValue(false));
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('runScript'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("runScript");
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEProject');
		$mockBuilder->setConstructorArgs(array($mockDatabase, $mockOperatingSystem));
		$mockBuilder->setMethods(array("getScripts"));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		$input = array('script' => 1);

		$this->object->runScript($input);

		$this->fail("runScript should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::runScript
	 */
	public function testRunScript_nothingFails() {
		$this->markTestIncomplete();
		$mockBuilder = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('acceptInput', 'renderCommand'));
		$mockScript = $mockBuilder->getMock();
		$mockScript->expects($this->once())->method('acceptInput');
		$mockScript->expects($this->once())->method('renderCommand');
		$scripts = array(1 => $mockScript);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('startTakingRequests', 'createRun', 'forgetAllRequests', 'giveRunPid', 'executeAllRequests'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method('startTakingRequests');
		$mockDatabase->expects($this->once())->method('createRun')->will($this->returnValue(1));
		$mockDatabase->expects($this->never())->method('forgetAllRequests');
		$mockDatabase->expects($this->once())->method('giveRunPid')->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method('executeAllRequests');
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('runScript'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("runScript");
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEProject');
		$mockBuilder->setConstructorArgs(array($mockDatabase, $mockOperatingSystem));
		$mockBuilder->setMethods(array("getScripts"));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		$input = array('script' => 1);
		$expected = "Able to validate script input-<br/>Script was started successfully";

		$actual = $this->object->runScript($input);

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::renderOverview
	 */
	public function testRenderOverview_zeroFormattedScripts() {
		$this->markTestIncomplete();
		$expected = "<div id=\"project_overview\">\n<div><span>cat1</span>{$mockScriptString}</div>\n<div><span>cat2</span>{$mockScriptString}{$mockScriptString}</div>\n</div>\n";
		$mockBuilder = $this->getMockBuilder('\Models\Scripts\DefaultScript');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getHtmlId', 'getScriptName', 'getScriptTitle'));
		$mockScript = $mockBuilder->getMock();
		$mockScript->expects($this->any())->method('getHtmlId')->will($this->returnValue('id'));
		$mockScript->expects($this->any())->method('getScriptName')->will($this->returnValue('name'));
		$mockScript->expects($this->any())->method('getScriptTitle')->will($this->returnValue('title'));
		$mockScriptString = "<span><a class=\"button\" onclick=\"displayHideables('{$mockScript->getHtmlId()}');\" title=\"{$mockScript->getScriptName()}\">{$mockScript->getScriptTitle()}</a></span>";
		$initialFormattedScripts = array(
			'cat1' => array($mockScript),
			'cat2' => array($mockScript, $mockScript),
		);
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEProject');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getFormattedScripts'));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method('getFormattedScripts')->will($this->returnValue($initialFormattedScripts));
		
		$actual = $this->object->renderOverview();

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::renderOverview
	 */
	public function testRenderOverview_oneFormattedScript() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\DefaultProject::renderOverview
	 */
	public function testRenderOverview_manyFormattedScripts_oneCategory() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\DefaultProject::renderOverview
	 */
	public function testRenderOverview_manyFormattedScripts_manyCategory() {
		$this->markTestIncomplete();
	}
}
