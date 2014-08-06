<?php

namespace Models;

class QIIMEProjectTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("QIIMEProjectTest");
	}

	private $owner = "asharp";
	private $id = 1;
	private $name = "Proj1";

	private $mockDatabase = NULL;
	private $mockOperatingSystem = NULL;
	private $object= NULL;
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
	 * @covers \Models\QIIMEProject::getInitialScripts
	 */
	public function testGetInitialScripts() {
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
	 * @covers \Models\DefaultProject::beginProject
	 */
	public function testBeginProject_databaseFails() {
		$expected = new \Exception("There was a problem with the database.");
		$actual = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "forgetAllRequests", "executeAllRequests", "createProject"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockDatabase->expects($this->once())->method("createProject")->will($this->returnValue(false));
		$this->object = new QIIMEProject($mockDatabase, $this->mockOperatingSystem);
		try {

			$this->object->beginProject();

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::beginProject
	 */
	public function testBeginProject_osFails() {
		$expected = new OperatingSystemException("message");
		$actual = NULL;
		$mockDatabase = $this->getMockBuilder('\Database\PDODatabase') 
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "forgetAllRequests", "executeAllRequests", "createProject", "getUserRoot"))
			->getMock();
		$mockOperatingSystem = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("createDir", "removeDirIfExists"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->once())->method("forgetAllRequests");
		$mockDatabase->expects($this->never())->method("executeAllRequests");
		$mockDatabase->expects($this->once())->method("createProject")->will($this->returnValue(1));
		$mockDatabase->expects($this->once())->method("getUserRoot");
		$mockOperatingSystem->expects($this->once())->method("createDir")->will($this->throwException(new OperatingSystemException("message")));
		$mockOperatingSystem->expects($this->once())->method("removeDirIfExists");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);
		try {

			$this->object->beginProject();

		}
		catch(\Exception $ex) {
			$actual = $ex;
		}
		$this->assertEquals($expected, $actual);
	} 
	/**
	 * @covers \Models\DefaultProject::beginProject
	 */
	public function testBeginProject_nothingFails() {
		$expecteds = array(
			'object_id' => 1,
		);
		$actuals = array();
		$mockDatabase  = $this->getMockBuilder('\Database\PDODatabase')
			->disableOriginalConstructor()
			->setMethods(array("startTakingRequests", "forgetAllRequests", "executeAllRequests", "createProject", "getUserRoot"))
			->getMock();
		$mockOperatingSystem  = $this->getMockBuilder('\Models\MacOperatingSystem')
			->disableOriginalConstructor()
			->setMethods(array("createDir", "removeDirIfExists"))
			->getMock();
		$mockDatabase->expects($this->once())->method("startTakingRequests");
		$mockDatabase->expects($this->never())->method("forgetAllRequests");
		$mockDatabase->expects($this->once())->method("executeAllRequests");
		$mockDatabase->expects($this->once())->method("createProject")->will($this->returnValue(1));
		$mockDatabase->expects($this->once())->method("getUserRoot");
		$mockOperatingSystem->expects($this->exactly(2))->method("createDir");
		$mockOperatingSystem->expects($this->never())->method("removeDirIfExists");
		$this->object = new QIIMEProject($mockDatabase, $mockOperatingSystem);

		$this->object->beginProject();

		$actuals['object_id'] = $this->object->getId();
		$this->assertEquals($expecteds, $actuals);
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
	 * @covers \Models\DefaultProject::retrieveAllBuiltInFiles
	 */
	public function testRetrieveAllBuiltInFiles_bothFoldersExist() {
		$this->markTestIncomplete();
		// TODO make sure lazy load is called right
		$expected = array(
			"/macqiime/greengenes/file1.txt",
			"/macqiime/greengenes/file2.txt",
			"/macqiime/greengenes/file3.txt",
			"/macqiime/UNITe/file1.txt",
			"/macqiime/UNITe/file2.txt",
			"/macqiime/UNITe/file3.txt",
		);
		$expecteds = array(
			"first_call" => $expected,
			"second_call" => $expected,
		);
		$actuals = array();
		$builtInFiles = array("file1.txt", "file2.txt", "file3.txt");
		$mockOperatingSystem  = $this->getMockBuilder("\Models\MacOperatingSystem")
			->disableOriginalConstructor()
			->setMethods(array('getDirContents'))
			->getMock();
		$mockOperatingSystem->expects($this->exactly(2))->method('getDirContents')->will($this->returnValue($builtInFiles));
		$this->object = new QIIMEProject($this->mockDatabase, $mockOperatingSystem);

		$actuals['first_call'] = $this->object->retrieveAllBuiltInFiles();
		$actuals['second_call'] = $this->object->retrieveAllBuiltInFiles();

		$this->assertEquals($expecteds, $actuals); 
	} 
	/**
	 * @covers \Models\DefaultProject::retrieveAllBuiltInFiles
	 */
	public function testRetrieveAllBuiltInFiles_neitherFolderExists() {
		$this->markTestIncomplete();
	}
	/**
	 * @covers \Models\DefaultProject::retrieveAllBuiltInFiles
	 */
	public function testRetrieveAllBuiltInFiles_onlyOneFolderExists() {
		$this->markTestIncomplete();
	}

	/**
	 * @covers \Models\QIIMEProject::getEnvironmentSource
	 */
	public function testGetEnvironmentSource() {
		$expected = "/macqiime/configs/bash_profile.txt";

		$actual = $this->object->getEnvironmentSource();

		$this->assertSame($expected, $actual);
	} 
}
