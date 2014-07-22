<?php

namespace Models\Scripts\Parameters;

class OldFileParameterTest extends \PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
		error_log("OldFileParameterTest");
	}

	private $owner;
	private $id;
	private $database;
	private $project;
	private $mockScript; 

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$stubGetter = new \Stubs\StubGetter();
		$this->mockScript = $stubGetter->getScript();
		$this->mockScript->expects($this->any())->method("getJsVar")->will($this->returnValue("js_script"));

		$this->owner = "sharpa";
		$this->id = 1;

		$pdo = new \PDO("sqlite:./data/database.sqlite");
		$pdo->exec("DELETE FROM uploaded_files");

		$this->database = new \Database\PDODatabase(new \Models\MacOperatingSystem());
		$this->database->createUploadedFile(
			$this->owner,	
			$this->id,
			"File1",
			"arbitrary_text"
		);
		$this->database->createUploadedFile(
			$this->owner,	
			$this->id,
			"File2",
			"map"
		);

		$operatingSystem = new \Models\MacOperatingSystem($this->database);
		$this->project = new \Models\QIIMEProject(
			$this->database,
			$operatingSystem
		);
		$this->project->setOwner($this->owner);
		$this->project->setId($this->id);
	}

	private $object;

	public function setUp() {
		$this->object = new OldFileParameter("--old_file_param", $this->project);
	}

	/**
	 * @test
	 * @covers OldFileParameter::__construct
	 * @covers OldFileParameter::isValueValid
	 */
	public function testConstructor() {
		$validValue1 = "File1";
		$validValue2 = "File2";
		$this->assertTrue($this->object->isValueValid($validValue1));
		$this->object->setValue($validValue1);
		$this->assertTrue($this->object->isValueValid($validValue2));
		$this->object->setValue($validValue2);

		$invalidValue = "File3";
		$this->assertFalse($this->object->isValueValid($invalidValue));
		$this->object->setValue($invalidValue);
	}

	public function testRenderForForm() {
		$this->object->setValue("File2");
		$expectedForm = "<label for=\"--old_file_param\">--old_file_param<select name=\"--old_file_param\">\n";
		$expectedForm .= "<option value=\"\">--Selected a file--</option>\n";
		$expectedForm .= "<optgroup label=\"arbitrary_text files\">\n";
		$expectedForm .= "<option value=\"File1\">File1</option>\n";
		$expectedForm .= "</optgroup>\n";
		$expectedForm .= "<optgroup label=\"map files\">\n";
		$expectedForm .= "<option value=\"File2\" selected>File2</option>\n";
		$expectedForm .= "</optgroup>\n";
		$expectedForm .= "</select></label>\n";

		$this->assertEquals($expectedForm, $this->object->renderForForm($disabled = false, $this->mockScript));
	}

}
