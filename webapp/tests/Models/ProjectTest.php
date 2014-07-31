<?php

namespace Models;

class ProjectTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("ProjectTest");
	}

	private $object= NULL;
	private $owner = "asharp";
	private $id = 1;
	private $name = "Proj1";

	private $newOwner = "bsharp";
	private $newId = 2;
	private $newName = "Proj2";

	private $mockDatabase = NULL;
	private $mockOperatingSystem = NULL;

	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockDatabase = $this->getMockBuilder('\Database\PDODatabase');
		$this->mockDatabase->disableOriginalConstructor();
		$this->mockDatabase = $this->mockDatabase->getMock();
		$this->mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem');
		$this->mockOperatingSystem->disableOriginalConstructor();
		$this->mockOperatingSystem = $this->mockOperatingSystem->getMock();
	}

	public function setUp() {
		$this->object = new QIIMEProject($this->mockDatabase, $this->mockOperatingSystem);
	}

	/**
	 * @covers \Models\DefaultProject::getOwner
	 * @covers \Models\DefaultProject::setOwner
	 */
	public function testOwner() {
		$expecteds = array(
			'default' => "",
			'after_set' => $this->owner,
		);
		$actuals = array();
		$actuals['default'] = $this->object->getOwner();
		
		$this->object->setOwner($this->owner);

		$actuals['after_set'] = $this->object->getOwner();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\DefaultProject::setOwner
	 * @covers \Models\DefaultProject::getProjectDir
	 */
	public function testOwner_lazyLoaderIsReset() {
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')->disableOriginalConstructor()->getMock();
		$mockDatabase->expects($this->exactly(2))->method('getUserRoot')->will($this->returnArgument(0));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$this->object->setOwner($this->owner);
		$unexpected = $this->object->getProjectDir();
		$this->object->setOwner($this->newOwner);

		$actual = $this->object->getProjectDir();

		$this->assertNotEquals($unexpected, $actual);
	}
	
	/**
	 * @covers \Models\DefaultProject::getId
	 * @covers \Models\DefaultProject::setId
	 */
	public function testId() {
		$expecteds = array(
			'default' => 0,
			'after_set' => $this->id,
		);
		$actuals = array();
		$actuals['default'] = $this->object->getId();

		$this->object->setId($this->id);

		$actuals['after_set'] = $this->object->getId();
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers \Models\DefaultProject::setId
	 * @covers \Models\DefaultProject::getProjectDir
	 */
	public function testId_lazyLoaderIsReset() {
		$this->object->setId($this->id);
		$unexpected = $this->object->getProjectDir();
		$this->object->setId($this->newId);

		$actual = $this->object->getProjectDir();

		$this->assertNotEquals($unexpected, $actual);
	}

	/**
	 * @covers \Models\DefaultProject::setName
	 * @covers \Models\DefaultProject::getName
	 */
	public function testName() {
		$expecteds = array(
			'default' => "",
			'after_set' => $this->name,
		);
		$actuals = array();
		$actuals['default'] = $this->object->getName();

		$this->object->setName($this->name);

		$actuals['after_set'] = $this->object->getName();
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
	 * @covers \Models\DefaultProject::getEnvironmentSource
	 */
	public function testGetEnvironmentSource() {
		$expected = "/macqiime/configs/bash_profile.txt";

		$actual = $this->object->getEnvironmentSource();

		$this->assertSame($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::getScripts
	 */
	public function testGetScripts() {
		$expected = array(1, 2, 3);
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEProject');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getInitialScripts'));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method('getInitialScripts')->will($this->returnValue($expected));

		$actual = $this->object->getScripts();
		$actual = $this->object->getScripts();

		$this->assertEquals($expected, $actual);
	} 
	public function testInitializeScripts() {
		$expected = array(
			'validate_mapping_file' => new \Models\Scripts\QIIME\ValidateMappingFile($this->object),
			'join_paired_ends' => new \Models\Scripts\QIIME\JoinPairedEnds($this->object),
			'split_libraries' => new \Models\Scripts\QIIME\SplitLibraries($this->object),
			'extract_barcodes' => new \Models\Scripts\QIIME\ExtractBarcodes($this->object),
			'split_libraries_fastq' => new \Models\Scripts\QIIME\SplitLibrariesFastq($this->object),
			'convert_fasta_qual_fastq' => new \Models\Scripts\QIIME\ConvertFastaQualFastq($this->object),
			'pick_otus' => new \Models\Scripts\QIIME\PickOtus($this->object),
			'pick_rep_set' => new \Models\Scripts\QIIME\PickRepSet($this->object),
			'assign_taxonomy' => new \Models\Scripts\QIIME\AssignTaxonomy($this->object),
			'make_otu_table' => new \Models\Scripts\QIIME\MakeOtuTable($this->object),
			'manipulate_table' => new \Models\Scripts\QIIME\ManipulateOtuTable($this->object),
			'align_seqs' => new \Models\Scripts\QIIME\AlignSeqs($this->object),
			'filter_alignment' => new \Models\Scripts\QIIME\FilterAlignment($this->object),
			'make_phylogeny' => new \Models\Scripts\QIIME\MakePhylogeny($this->object),
		);

		$actual = $this->object->getInitialScripts();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\DefaultProject::renderOverview
	 */
	public function testRenderOverview() {
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
		$expected = "<div id=\"project_overview\">\n<div><span>cat1</span>{$mockScriptString}</div>\n<div><span>cat2</span>{$mockScriptString}{$mockScriptString}</div>\n</div>\n";
		
		$actual = $this->object->renderOverview();

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::getFormattedScripts
	 */
	public function testGetFormattedScripts() {
		$expected = array(
			'Validate input' => array(
				new \Models\Scripts\QIIME\ValidateMappingFile($this->object),
			),
			'Prepare libraries' => array(
				new \Models\Scripts\QIIME\JoinPairedEnds($this->object),
				new \Models\Scripts\QIIME\SplitLibraries($this->object),
				new \Models\Scripts\QIIME\ExtractBarcodes($this->object),
				new \Models\Scripts\QIIME\SplitLibrariesFastq($this->object),
				new \Models\Scripts\QIIME\ConvertFastaQualFastq($this->object),
			),
			'Organize into OTUs' => array(
				new \Models\Scripts\QIIME\PickOtus($this->object),
				new \Models\Scripts\QIIME\PickRepSet($this->object),
			),
			'Count/analyze OTUs' => array(
				new \Models\Scripts\QIIME\AssignTaxonomy($this->object),
				new \Models\Scripts\QIIME\MakeOtuTable($this->object),
				new \Models\Scripts\QIIME\ManipulateOtuTable($this->object),
			),
			'Perform phylogeny analysis' => array(
				new \Models\Scripts\QIIME\AlignSeqs($this->object),
				new \Models\Scripts\QIIME\FilterAlignment($this->object),
				new \Models\Scripts\QIIME\MakePhylogeny($this->object),
			),
		);

		$actual = $this->object->getFormattedScripts();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\DefaultProject::getFileTypes
	 */
	public function testGetFileTypes() {
		$initials = array(1, 2, 3);
		$mockBuilder = $this->getMockBuilder("\Models\QIIMEProject");
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("getInitialFileTypes"));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method("getInitialFileTypes")->will($this->returnValue($initials));

		$actual = $this->object->getFileTypes();
		$actual = $this->object->getFileTypes();

		$expecteds = array(1, 2, 3, new ArbitraryTextFileType());
		$this->assertEquals($expecteds, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::getInitialFileTypes
	 */
	public function testGetInitialFileTypes() {
		$expected = array(
			new MapFileType(),
			new SequenceFileType(),
			new SequenceQualityFileType(),
			new FastqFileType(),
		);

		$actual = $this->object->getInitialFileTypes();

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::getFileTypeFromHtmlId
	 */
	public function testGetFileTypeFromHtmlId() {
		$expecteds = array(
			'map' => new MapFileType(),
			'sequence' => new SequenceFileType(),
			'quality' => new SequenceQualityFileType(),
			'fastq' => new FastqFileType(),
			'arbitrary_text' => new ArbitraryTextFileType(),
			'invalid_id' => NULL,
		);
		$actuals = array();

		foreach (array_keys($expecteds) as $id) {
			$actuals[$id] = $this->object->getFileTypeFromHtmlId($id);
		}

		$this->assertEquals($expecteds, $actuals);
	} 
	
	/**
	 * @covers \Models\DefaultProject::getProjectDir
	 */
	public function testGetProjectDir() {
		$expected = "u{$this->owner}/p{$this->id}";
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getUserRoot'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method('getUserRoot')->will($this->returnArgument(0));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		$this->object->setOwner($this->owner);
		$this->object->setId($this->id);

		$actual = $this->object->getProjectDir();
		$actual = $this->object->getProjectDir();

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::retrieveAllUploadedFiles
	 */
	public function testRetrieveAllUploadedFiles_zeroUploadedFiles() {
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
	 * @covers \Models\DefaultProject::getPastScriptRuns
	 */
	public function testGetPastScriptRuns_zeroRuns() {
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
	 * @covers \Models\DefaultProject::retrieveAllGeneratedFiles
	 */
	public function testRetrieveAllGeneratedFiles_noRuns() {
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
	 * @covers \Models\DefaultProject::retrieveAllBuiltInFiles
	 */
	public function testRetrieveAllBuiltInFiles() {
		$mockBuilder = $this->getMockBuilder("\Models\MacOperatingSystem");
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('getDirContents'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$builtInFiles = array("file1.txt", "file2.txt", "file3.txt");
		$mockOperatingSystem->expects($this->exactly(2))->method('getDirContents')->will($this->returnValue($builtInFiles));
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);
		$expecteds = array(
			"/macqiime/greengenes/file1.txt",
			"/macqiime/greengenes/file2.txt",
			"/macqiime/greengenes/file3.txt",
			"/macqiime/UNITe/file1.txt",
			"/macqiime/UNITe/file2.txt",
			"/macqiime/UNITe/file3.txt",
		);

		$actuals = $this->object->retrieveAllBuiltInFiles();

		$this->assertEquals($expecteds, $actuals); 
	} 

	/**
	 * @covers \Models\DefaultProject::beginProject
	 * @expectedException \Exception
	 */
	public function testBeginProject_databaseFails() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("startTakingRequests", "forgetAllRequests", "executeAllRequests", "createProject"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockDatabase->expects($this->once())->method("createProject")->will($this->returnValue(false));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);

		$this->object->beginProject();
	} 
	/**
	 * @covers \Models\DefaultProject::beginProject
	 * @expectedException \Models\OperatingSystemException
	 */
	public function testBeginProject_osFails() {
		$expecteds = array(
			'object_id' => 1,
		);
		$actuals = array();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("startTakingRequests", "forgetAllRequests", "executeAllRequests", "createProject", "getUserRoot"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockDatabase->expects($this->once())->method("createProject")->will($this->returnValue(1));
		$mockDatabase->expects($this->once())->method("getUserRoot");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("createDir", "removeDirIfExists"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("createDir")->will($this->throwException(new OperatingSystemException()));
		$mockOperatingSystem->expects($this->once())->method("removeDirIfExists");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->beginProject();

		$this->fail("beginProject should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::beginProject
	 */
	public function testBeginProject_nothingFails() {
		$expecteds = array(
			'object_id' => 1,
		);
		$actuals = array();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("startTakingRequests", "forgetAllRequests", "executeAllRequests", "createProject", "getUserRoot"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockDatabase->expects($this->once())->method("createProject")->will($this->returnValue(1));
		$mockDatabase->expects($this->once())->method("getUserRoot");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("createDir", "removeDirIfExists"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->exactly(2))->method("createDir");
		$mockOperatingSystem->expects($this->never())->method("removeDirIfExists");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->beginProject();

		$actuals['object_id'] = $this->object->getId();
		$this->assertEquals($expecteds, $actuals);
	} 

	/**
	 * @covers \Models\DefaultProject::receiveDownloadedFile
	 * @expectedException \Exception
	 */
	public function testReceiveDownloadedFile_databaseFails() {
		$fileType = $this->getMockBuilder('\Models\FileType')->getMockForAbstractClass();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(false));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);

		$this->object->receiveDownloadedFile("url", "fileName", $fileType);

		$this->fail("receiveDownloadedFile should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::receiveDownloadedFile
	 * @expectedException \Models\OperatingSystemException
	 */
	public function testReceiveDownloadedFile_osFails() {
		$fileType = $this->getMockBuilder('\Models\FileType')->getMockForAbstractClass();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("downloadFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("downloadFile")->will($this->throwException(new OperatingSystemException()));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->receiveDownloadedFile("url", "fileName", $fileType);

		$this->fail("receiveDownloadedFile should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::receiveDownloadedFile
	 */
	public function testReceiveUploadedFile_nothingFails() {
		$fileType = $this->getMockBuilder('\Models\FileType')->getMockForAbstractClass();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("downloadFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$expected = "console output";
		$mockOperatingSystem->expects($this->once())->method("downloadFile")->will($this->returnValue($expected));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->receiveDownloadedFile("url", "fileName", $fileType);

		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::receiveDownloadedFile
	 */
	public function testReceiveUploadedFile_lazyLoaderIsReset() {
		$fileType = $this->getMockBuilder('\Models\FileType')->getMockForAbstractClass();
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("startTakingRequests", "createUploadedFile", "forgetAllRequests", "executeAllRequests", "getAllUploadedFiles"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockDatabase->expects($this->exactly(2))->method("getAllUploadedFiles")->will($this->returnValue(array(1,2)));
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array("downloadFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$expected = "console output";
		$mockOperatingSystem->expects($this->once())->method("downloadFile")->will($this->returnValue($expected));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->retrieveAllUploadedFiles();
		$actual = $this->object->receiveDownloadedFile("url", "fileName", $fileType);
		$this->object->retrieveAllUploadedFiles();

		$this->assertEquals($expected, $actual);
	} 

	/**
	 * @covers \Models\DefaultProject::runScript
	 * @expectedException \Exception
	 */
	public function testRunScript_invalidScriptId() {
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
	 * @covers \Models\DefaultProject::runScript
	 */
	public function testRunScript_lazyLoadersAreReset() {
		$mockBuilder = $this->getMockBuilder('\Models\Scripts\QIIME\ValidateMappingFile');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('acceptInput', 'renderCommand'));
		$mockScript = $mockBuilder->getMock();
		$mockScript->expects($this->once())->method('acceptInput');
		$mockScript->expects($this->once())->method('renderCommand');
		$scripts = array(1 => $mockScript);
		$allRuns = array(
			array("id" => 1, "script_name" => "name", "script_string" => "string", "run_status" => -1, "deleted" => 0),
		);
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('startTakingRequests', 'createRun', 'forgetAllRequests', 'giveRunPid', 'executeAllRequests',
			'getAllRuns'));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method('startTakingRequests');
		$mockDatabase->expects($this->once())->method('createRun')->will($this->returnValue(1));
		$mockDatabase->expects($this->never())->method('forgetAllRequests');
		$mockDatabase->expects($this->once())->method('giveRunPid')->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method('executeAllRequests');
		$mockDatabase->expects($this->exactly(4))->method('getAllRuns')->will($this->returnValue($allRuns));
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem');
		$mockBuilder->disableOriginalConstructor();
		$mockBuilder->setMethods(array('runScript'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("runScript");
		$mockBuilder = $this->getMockBuilder('\Models\QIIMEProject');
		$mockBuilder->setConstructorArgs(array($mockDatabase, $mockOperatingSystem));
		$mockBuilder->setMethods(array("getScripts", "attemptGetDirContents", "getProjectDir"));
		$this->object = $mockBuilder->getMock();
		$this->object->expects($this->once())->method("getScripts")->will($this->returnValue($scripts));
		$this->object->expects($this->exactly(4))->method("attemptGetDirContents")->will($this->returnValue(array(1)));
		$this->object->expects($this->exactly(4))->method("getProjectDir");
		$input = array('script' => 1);
		$expected = "Able to validate script input-<br/>Script was started successfully";

		$this->object->getPastScriptRuns();
		$this->object->retrieveAllGeneratedFiles();
		$actual = $this->object->runScript($input);
		$this->object->getPastScriptRuns();
		$this->object->retrieveAllGeneratedFiles();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\DefaultProject::deleteUploadedFile
	 * @expectedException \Exception
	 */
	public function testDeleteUploadedFile_dbFails() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(false));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("deleteFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->never())->method("deleteFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->deleteUploadedFile("fileName");

		$this->fail("deleteUpoadedFile should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::deleteUploadedFile
	 * @expectedException \Models\OperatingSystemException
	 */
	public function testDeleteUploadedFile_osFails() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("deleteFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("deleteFile")->will($this->throwException(new OperatingSystemException()));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->deleteUploadedFile("fileName");

		$this->fail("deleteUpoadedFile should have thrown an exception");
	} 
	/**
	 * @covers \Models\DefaultProject::deleteUploadedFile
	 */
	public function testDeleteUploadedFile_nothingFails() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("deleteFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("deleteFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->deleteUploadedFile("fileName");
	} 
	
	/**
	 * @covers \Models\DefaultProject::deleteGeneratedFile
	 */
	public function testDeleteGeneratedFile() {
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('deleteFile'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method('deleteFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$this->object->deleteGeneratedFile("filename", "runId");
	} 
	/**
	 * @covers \Models\DefaultProject::unzipUploadedFile
	 * @expectedException \Exception
	 */
	public function testUnzipUploadedFile_dbRemoveFails() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "createUploadedFile", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile");
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("createUploadedFile");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("unzipFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->never())->method("unzipFile");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->unzipUploadedFile("fileName");

		$this->fail("unzipUploadedFile should have thrown and exception");
	} 
	/**
	 * @covers \Models\DefaultProject::unzipUploadedFile
	 * @expectedException \Models\OperatingSystemException
	 */
	public function testUnzipUploadedFile_osFails() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "createUploadedFile", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("createUploadedFile");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("unzipFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("unzipFile")->will($this->throwException(new OperatingSystemException()));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->unzipUploadedFile("fileName");

		$this->fail("unzipUploadedFile should have thrown and exception");
	} 
	/**
	 * @covers \Models\DefaultProject::unzipUploadedFile
	 * @expectedException \Exception
	 */
	public function testUnzipUploadedFile_dbCreateFails() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "createUploadedFile", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("createUploadedFile")->will($this->returnValue(false));
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("unzipFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("unzipFile")->will($this->returnValue(array(1, 2)));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->unzipUploadedFile("fileName");

		$this->fail("unzipUploadedFile should have thrown and exception");
	} 
	/**
	 * @covers \Models\DefaultProject::unzipUploadedFile
	 */
	public function testUnzipUploadedFile_nothingFails() {
		$mockBuilder = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "removeUploadedFile", "forgetAllRequests", "createUploadedFile", "executeAllRequests"));
		$mockDatabase = $mockBuilder->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("removeUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->exactly(2))->method("createUploadedFile")->will($this->returnValue(true));
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("unzipFile"));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method("unzipFile")->will($this->returnValue(array(1, 2)));
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$actual = $this->object->unzipUploadedFile("fileName");

		$this->assertTrue($actual);
	} 
	/**
	 * @covers \Models\DefaultProject::unzipGeneratedFile
	 */
	public function testUnzipGeneratedFile() {
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('unzipFile'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method('unzipFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$this->object->unzipGeneratedFile("filename", "runId");
	} 
	/**
	 * @covers \Models\DefaultProject::compressUploadedFile
	 * @expectedException \Exception
	 */
	public function testCompressUploadedFile_dbFails() {
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
	 * @covers \Models\DefaultProject::compressGeneratedFile
	 */
	public function testCompressGeneratedFile() {
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('compressFile'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method('compressFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$this->object->compressGeneratedFile("filename", "runId");
	} 
	/**
	 * @covers \Models\DefaultProject::decompressUploadedFile
	 * @expectedException \Exception
	 */
	public function testDecompressUploadedFile_dbFails() {
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
	 * @covers \Models\DefaultProject::decompressGeneratedFile
	 */
	public function testDecompressGeneratedFile() {
		$mockBuilder = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array('decompressFile'));
		$mockOperatingSystem = $mockBuilder->getMock();
		$mockOperatingSystem->expects($this->once())->method('decompressFile');
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$this->object->decompressGeneratedFile("filename", "runId");
	} 
}
