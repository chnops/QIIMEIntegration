<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;

class AssignTaxonomyTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("AssignTaxonomyTest");
	}

	private $defaultValue = 1;

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $emptyInput = array(
		"--input_fasta_fp" => "",
		"--assignment_method" => "",
		"--read_1_seqs_fp" => "",
		"--read_2_seqs_fp" => "",
		"--single_ok" => "",
		"--no_single_ok_generic" => "",
		"--read_id_regex" => "",
		"--amplicon_id_regex" => "",
		"--header_id_regex" => "",
		"--confidence" => "",
		"--rdp_max_memory" => "",
		"--uclust_min_consensus_fraction" => "",
		"--uclust_similarity" => "",
		"--uclust_max_accepts" => "",
		"--id_to_taxonomy_fp" => "",
		"--tree_fp" => "",
		"--reference_seqs_fp" => "",
		"--blast_db" => "",
		"__--reference_seqs_fp__--blast_db__" => "",
		"--e_value" => "",
		"--training_data_properties_fp" => "",
		"--verbose" => "",
		"--output_dir" => "",
	);
	private $mockProject = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->setMethods(array("retrieveAllUploadedFiles", "retrieveAllGeneratedFiles", "retrieveAllBuiltInFiles"))
			->getMockForAbstractClass();
		$this->mockProject->expects($this->any())->method("retrieveAllUploadedFiles")->will($this->returnValue(array("uploaded_file")));
		$this->mockProject->expects($this->any())->method("retrieveAllGeneratedFiles")->will($this->returnValue(array("generated_file")));
		$this->mockProject->expects($this->any())->method("retrieveAllBuildInFiles")->will($this->returnValue(array("built_in_file")));
	}
	public function setUp() {
		$this->object = new \Models\Scripts\QIIME\AssignTaxonomy($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\AssignTaxonomy::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "assign_taxonomy.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\AssignTaxonomy::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Assign taxonomies";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\AssignTaxonomy::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "assign_taxonomy";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testInputFastaFp_notPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --input_fasta_fp is required</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testInputFastaFp_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;

		$this->object->acceptInput($input);

	}

	public function testRead1SeqsFp_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rtax";
		$input['--read_1_seqs_fp'] = true;

		$this->object->acceptInput($input);

	}
	public function testRead1SeqsFp_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --read_1_seqs_fp can only be used when:<br/>&nbsp;- --assignment_method is set to rtax</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--read_1_seqs_fp'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	
	public function testRead2SeqsFp_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rtax";
		$input['--read_2_seqs_fp'] = true;

		$this->object->acceptInput($input);

	}
	public function testRead2SeqsFp_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --read_2_seqs_fp can only be used when:<br/>&nbsp;- --assignment_method is set to rtax</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--read_2_seqs_fp'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testSingleOk_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rtax";
		$input['--single_ok'] = true;

		$this->object->acceptInput($input);

	}
	public function testSingleOk_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --single_ok can only be used when:<br/>&nbsp;- --assignment_method is set to rtax</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--single_ok'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testNoSingleOkGeneric_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rtax";
		$input['--no_single_ok_generic'] = true;

		$this->object->acceptInput($input);

	}
	public function testNoSingleOkGeneric_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --no_single_ok_generic can only be used when:<br/>&nbsp;- --assignment_method is set to rtax</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--no_single_ok_generic'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testReadIdRegex_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rtax";
		$input['--read_id_regex'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testReadIdRegex_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --read_id_regex can only be used when:<br/>&nbsp;- --assignment_method is set to rtax</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--read_id_regex'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testAmpliconIdRegex_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rtax";
		$input['--amplicon_id_regex'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testAmpliconIdRegex_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --amplicon_id_regex can only be used when:<br/>&nbsp;- --assignment_method is set to rtax</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--amplicon_id_regex'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testHeaderIdRegex_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rtax";
		$input['--header_id_regex'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testHeaderIdRegex_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --header_id_regex can only be used when:<br/>&nbsp;- --assignment_method is set to rtax</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--header_id_regex'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testConfidence_allowed_mothur() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "mothur";
		$input['--confidence'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testConfidence_allowed_rdp() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--confidence'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testConfidence_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --confidence can only be used when:<br/>&nbsp;- --assignment_method is set to mothur<br/>&nbsp;- --assignment_method is set to rdp</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rtax";
		$input['--confidence'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testRdpMaxMemory_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--rdp_max_memory'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testRdpMaxMemory_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --rdp_max_memory can only be used when:<br/>&nbsp;- --assignment_method is set to rdp</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rtax";
		$input['--rdp_max_memory'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testUclustMinConsensusFraction_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "uclust";
		$input['--uclust_min_consensus_fraction'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testUclustMinConsensusFraction_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --uclust_min_consensus_fraction can only be used when:<br/>&nbsp;- --assignment_method is set to uclust</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--uclust_min_consensus_fraction'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testUclustSimilarity_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "uclust";
		$input['--uclust_similarity'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testUclustSimilarity_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --uclust_similarity can only be used when:<br/>&nbsp;- --assignment_method is set to uclust</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--uclust_similarity'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testUclustMaxAccepts_allowed() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "uclust";
		$input['--uclust_max_accepts'] = $this->defaultValue;

		$this->object->acceptInput($input);

	}
	public function testUclustMaxAccepts_notAllowed() {
		$expected = $this->errorMessageIntro . "<li>The parameter --uclust_max_accepts can only be used when:<br/>&nbsp;- --assignment_method is set to uclust</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--uclust_max_accepts'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testIdToTaxonomyFp_notRequired_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--id_to_taxonomy_fp'] = "built_in_file";

		$this->object->acceptInput($input);

	}
	public function testIdToTaxonomyFp_notRequired_notPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";

		$this->object->acceptInput($input);

	}
	public function testIdToTaxonomyFp_required_present() {
		$expected = $this->errorMessageIntro . "<li>The parameter --id_to_taxonomy_fp is required when:<br/>&nbsp;- --assignment_method is set to blast</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "blast";
		$input['__--reference_seqs_fp__--blast_db__'] = "--blast_db";
		unset($input['--reference_seqs_fp']);
		$input['--blast_db'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testIdToTaxonomyFp_requireed_present() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "blast";
		$input['__--reference_seqs_fp__--blast_db__'] = "--blast_db";
		$input['--blast_db'] = true;
		unset($input['--reference_seqs_fp']);
		$input['--id_to_taxonomy_fp'] = "built_in_file";

		$this->object->acceptInput($input);

	}

	public function testReferenceSeqsFpOrBlastDB_shouldBeSeqs_isSeqs() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['__--reference_seqs_fp__--blast_db__'] = "--reference_seqs_fp";
		$input['--reference_seqs_fp'] = true;
		unset($input['--blast_db']);

		$this->object->acceptInput($input);

	}
	public function testReferenceSeqsFpOrBlastDB_shouldBeSeqs_isBlastDb() {
		$expected = $this->errorMessageIntro . "<li>Since __--reference_seqs_fp__--blast_db__ is set to --reference_seqs_fp, that parameter must be specified.</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['__--reference_seqs_fp__--blast_db__'] = "--reference_seqs_fp";
		unset($input['--reference_seqs_fp']);
		$input['--blast_db'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testReferenceSeqsFpOrBlastDB_shouldBeSeqs_isNeither() {
		$expected = $this->errorMessageIntro . "<li>Since __--reference_seqs_fp__--blast_db__ is set to --reference_seqs_fp, that parameter must be specified.</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['__--reference_seqs_fp__--blast_db__'] = "--reference_seqs_fp";
		unset($input['--reference_seqs_fp']);
		unset($input['--blast_db']);
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testReferenceSeqsFpOrBlastDB_shouldBeSeqs_isBoth() {
		$expected = $this->errorMessageIntro . "<li>Since __--reference_seqs_fp__--blast_db__ is set to --reference_seqs_fp, --blast_db is not allowed.</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['__--reference_seqs_fp__--blast_db__'] = "--reference_seqs_fp";
		$input['--reference_seqs_fp'] = true;
		$input['--blast_db'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testTreeFp_assignmentMethodIsTax2Tree_isPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "tax2tree";
		$input['--tree_fp'] = true;

		$this->object->acceptInput($input);

	}
	public function testTreeFp_assignmentMethodIsTax2Tree_isNotPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --tree_fp is required when:<br/>&nbsp;- --assignment_method is set to tax2tree</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "tax2tree";
		unset($input['--tree_fp']);
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testTreeFp_assignmentMethodIsNotTax2Tree_isPresent() {
		$expected = $this->errorMessageIntro . "<li>The parameter --tree_fp can only be used when:<br/>&nbsp;- --assignment_method is set to tax2tree</li>" . $this->errorMessageOutro;
		$actual = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		$input['--tree_fp'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch (ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testTreeFp_assignmentMethodIsNotTax2Tree_isNotPresent() {
		$expected = "";
		$input = $this->emptyInput;
		$input['--input_fasta_fp'] = true;
		$input['--assignment_method'] = "rdp";
		unset($input['--tree_fp']);

		$this->object->acceptInput($input);

	}
}
