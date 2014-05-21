<?php

namespace Models\Scripts;

class OldFileParameterTest extends \PHPUnit_Framework_TestCase {

	private static $database;
	private static $owner;
	private static $id;
	private static $project;

	public static function setUpBeforeClass() {
		error_log("OldFileParameterTest");
		OldFileParameterTest::$owner = "sharpa";
		OldFileParameterTest::$id = 1;

		$pdo = new \PDO("sqlite:./data/database.sqlite");
		$pdo->exec("DELETE FROM uploaded_files");

		OldFileParameterTest::$database = new \Database\PDODatabase(new \Models\MacOperatingSystem());
		OldFileParameterTest::$database->createUploadedFile(
			OldFileParameterTest::$owner,	
			OldFileParameterTest::$id,
			"File1",
			"arbitrary_text"
		);
		OldFileParameterTest::$database->createUploadedFile(
			OldFileParameterTest::$owner,	
			OldFileParameterTest::$id,
			"File2",
			"map"
		);

		$operatingSystem = new \Models\MacOperatingSystem(OldFileParameterTest::$database);
		OldFileParameterTest::$project = new \Models\QIIMEProject(
			OldFileParameterTest::$database,
			new \Models\QIIMEWorkflow(OldFileParameterTest::$database, $operatingSystem),
			$operatingSystem
		);
		OldFileParameterTest::$project->setOwner(OldFileParameterTest::$owner);
		OldFileParameterTest::$project->setId(OldFileParameterTest::$id);
	}

	private $parameter;

	public function setUp() {
		$this->parameter = new OldFileParameter("--old_file_param", OldFileParameterTest::$project);
	}

	/**
	 * @test
	 * @covers OldFileParameter::__construct
	 * @covers OldFileParameter::isValueValid
	 */
	public function testConstructor() {
		$validValue1 = "File1";
		$validValue2 = "File2";
		$this->parameter->setValue($validValue1);
		$this->assertTrue($this->parameter->isValueValid());
		$this->parameter->setValue($validValue2);
		$this->assertTrue($this->parameter->isValueValid());

		$invalidValue = "File3";
		$this->parameter->setValue($invalidValue);
		$this->assertFalse($this->parameter->isValueValid());
	}

	public function testRenderForForm() {
		$this->parameter->setValue("File2");
		$expectedForm = "<label for=\"--old_file_param\">--old_file_param<select name=\"--old_file_param\">\n";
		$expectedForm .= "<option value=\"\">--Selected a file--</option>\n";
		$expectedForm .= "<optgroup label=\"arbitrary_text files\">\n";
		$expectedForm .= "<option value=\"File1\">File1</option>\n";
		$expectedForm .= "</optgroup>\n";
		$expectedForm .= "<optgroup label=\"map files\">\n";
		$expectedForm .= "<option value=\"File2\" selected>File2</option>\n";
		$expectedForm .= "</optgroup>\n";
		$expectedForm .= "</select></label>\n";

		$this->assertEquals($expectedForm, $this->parameter->renderForForm());
	}

}
