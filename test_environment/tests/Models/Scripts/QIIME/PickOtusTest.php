<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\QIIME;
use Models\Scripts\ScriptException;
use Models\Scripts\Parameters\DefaultParameter;

class PickOtusTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("PickOtusTest");
	}

	private $defaultValue = 1;

	private $errorMessageIntro = "There were some problems with the parameters you submitted:<ul>";
	private $errorMessageOutro = "</ul>\n";
	private $mockProject = NULL;
	private $object = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);

		$this->mockProject = $this->getMockBuilder('\Models\DefaultProject')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}
	public function setUp() {
		$this->object = new \Models\Scripts\QIIME\PickOtus($this->mockProject);
	}

	/**
	 * @covers \Models\Scripts\QIIME\PickOtus::getScriptName
	 */
	public function testGetScriptName() {
		$expected = "pick_otus.py";

		$actual = $this->object->getScriptName();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\PickOtus::getScriptTitle
	 */
	public function testGetScriptTitle() {
		$expected = "Pick OTUs";

		$actual = $this->object->getScriptTitle();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\Scripts\QIIME\PickOtus::getHtmlId
	 */
	public function testGetHtmlId() {
		$expected = "pick_otus";

		$actual = $this->object->getHtmlId();

		$this->assertEquals($expected, $actual);
	}

	public function testScriptExists() {
		$expecteds = array(
			"script_location" => "/macqiime/QIIME/bin/{$this->object->getScriptName()}",
			"which_return" => "0",
		);
		$actuals = array();
		$mockProject = $this->getMockBuilder('\Models\QIIMEProject')
			->disableOriginalConstructor()
			->setMethods(NULL)
			->getMock();
		$sourceFile = $mockProject->getEnvironmentSource();
		$systemCommand = "source {$sourceFile}; which {$this->object->getScriptName()}; echo $?";

		exec($systemCommand, $output);

		$actuals['script_location'] = $output[0];
		$actuals['which_return'] = $output[1];
		$this->assertEquals($expecteds, $actuals);
	}

	public function testRequireds_present() {
		$input = array();
		$input['--input_seqs_filepath'] = true;

		$this->object->acceptInput($input);
	}
	public function testRequireds_notPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testTriePrefilter_triggerPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--trie_prefilter'] = true;
		$input['--prefix_prefilter_length'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testTriePrefilter_triggerNotPresent() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>The parameter --prefix_prefilter_length can only be used when:<br/>&nbsp;- --trie_prefilter is set</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		unset($input['--trie_prefilter']);
		$input['--prefix_prefilter_length'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testOtuPickingMethod_uclust_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "uclust";
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--suppress_presort_by_abundance_uclust'] = true;
		$input['--optimal_uclust'] = true;
		$input['--exact_uclust'] = true;
		$input['--user_sort'] = true;
		$input['--stepwords'] = $this->defaultValue;
		$input['--uclust_otu_id_prefix'] = $this->defaultValue;
		$input['--suppress_uclust_stable_sort'] = true;
		$input['--suppress_uclust_prefilter_exact_match'] = true;
		$input['--save_uc_files'] = true;
		$input['--max_accepts'] = $this->defaultValue;
		$input['--max_rejects'] = $this->defaultValue;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = "--enable_rev_strand_match";
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_uclust_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --similarity can only be used when:<br/>&nbsp;- --otu_picking_method is set to cdhit<br/>&nbsp;- --otu_picking_method is set to blast" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --word_length can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_presort_by_abundance_uclust can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --optimal_uclust can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --exact_uclust can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --user_sort can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --stepwords can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --uclust_otu_id_prefix can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --suppress_uclust_stable_sort can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --suppress_uclust_prefilter_exact_match can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --save_uc_files can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --max_accepts can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --max_rejects can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter __--usearch_fast_cluster__--enable_rev_strand_match__ can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "uclust";
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--suppress_presort_by_abundance_uclust'] = true;
		$input['--optimal_uclust'] = true;
		$input['--exact_uclust'] = true;
		$input['--user_sort'] = true;
		$input['--stepwords'] = $this->defaultValue;
		$input['--uclust_otu_id_prefix'] = $this->defaultValue;
		$input['--suppress_uclust_stable_sort'] = true;
		$input['--suppress_uclust_prefilter_exact_match'] = true;
		$input['--save_uc_files'] = true;
		$input['--max_accepts'] = $this->defaultValue;
		$input['--max_rejects'] = $this->defaultValue;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = "--enable_rev_strand_match";
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testOtuPickingMethod_uclustRef_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "uclust_ref";
		$input['--refseqs_fp'] = true;
		$input['--suppress_new_clusters'] = true;
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--suppress_presort_by_abundance_uclust'] = true;
		$input['--optimal_uclust'] = true;
		$input['--exact_uclust'] = true;
		$input['--user_sort'] = true;
		$input['--stepwords'] = $this->defaultValue;
		$input['--uclust_otu_id_prefix'] = $this->defaultValue;
		$input['--suppress_uclust_stable_sort'] = true;
		$input['--suppress_uclust_prefilter_exact_match'] = true;
		$input['--save_uc_files'] = true;
		$input['--max_accepts'] = $this->defaultValue;
		$input['--max_rejects'] = $this->defaultValue;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = "--enable_rev_strand_match";
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_uclustRef_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --refseqs_fp can only be used when:<br/>&nbsp;- --otu_picking_method is set to blast<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch_ref<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_new_clusters can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch_ref<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --similarity can only be used when:<br/>&nbsp;- --otu_picking_method is set to cdhit<br/>&nbsp;- --otu_picking_method is set to blast" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --word_length can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_presort_by_abundance_uclust can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --optimal_uclust can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --exact_uclust can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --user_sort can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --stepwords can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --uclust_otu_id_prefix can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --suppress_uclust_stable_sort can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --suppress_uclust_prefilter_exact_match can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --save_uc_files can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --max_accepts can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --max_rejects can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter __--usearch_fast_cluster__--enable_rev_strand_match__ can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "uclust_ref";
		$input['--refseqs_fp'] = true;
		$input['--suppress_new_clusters'] = true;
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--suppress_presort_by_abundance_uclust'] = true;
		$input['--optimal_uclust'] = true;
		$input['--exact_uclust'] = true;
		$input['--user_sort'] = true;
		$input['--stepwords'] = $this->defaultValue;
		$input['--uclust_otu_id_prefix'] = $this->defaultValue;
		$input['--suppress_uclust_stable_sort'] = true;
		$input['--suppress_uclust_prefilter_exact_match'] = true;
		$input['--save_uc_files'] = true;
		$input['--max_accepts'] = $this->defaultValue;
		$input['--max_rejects'] = $this->defaultValue;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = "--enable_rev_strand_match";
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_blast_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "blast";
		$input['--blast_db'] = true;
		$input['--min_aligned_percent'] = $this->defaultValue;
		$input['--max_e_value'] = $this->defaultValue;
		$input['--refseqs_fp'] = true;
		$input['--similarity'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_blast_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --blast_db can only be used when:<br/>&nbsp;- --otu_picking_method is set to blast" .
				"</li>" .
			"<li>The parameter --min_aligned_percent can only be used when:<br/>&nbsp;- --otu_picking_method is set to blast" .
				"</li>" .
			"<li>The parameter --max_e_value can only be used when:<br/>&nbsp;- --otu_picking_method is set to blast" .
				"</li>" .
			"<li>The parameter --refseqs_fp can only be used when:<br/>&nbsp;- --otu_picking_method is set to blast<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch_ref<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --similarity can only be used when:<br/>&nbsp;- --otu_picking_method is set to cdhit<br/>&nbsp;- --otu_picking_method is set to blast" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "blast";
		$input['--blast_db'] = true;
		$input['--min_aligned_percent'] = $this->defaultValue;
		$input['--max_e_value'] = $this->defaultValue;
		$input['--refseqs_fp'] = true;
		$input['--similarity'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_mothur_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "mothur";
		$input['--clustering_algorithm'] = "furthest";
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_mothur_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --clustering_algorithm can only be used when:<br/>&nbsp;- --otu_picking_method is set to mothur" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "mothur";
		$input['--clustering_algorithm'] = "furthest";
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_cdhit_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "cdhit";
		$input['--max_cdhit_memory'] = $this->defaultValue;
		$input['--similarity'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_cdhit_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --max_cdhit_memory can only be used when:<br/>&nbsp;- --otu_picking_method is set to cdhit" .
				"</li>" .
			"<li>The parameter --similarity can only be used when:<br/>&nbsp;- --otu_picking_method is set to cdhit<br/>&nbsp;- --otu_picking_method is set to blast" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "cdhit";
		$input['--max_cdhit_memory'] = $this->defaultValue;
		$input['--similarity'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_usearch_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>Since __--usearch_fast_cluster__--enable_rev_strand_match__ is set to --usearch_fast_cluster, --enable_rev_strand_match is not allowed.</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "usearch";
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--usearch_fast_cluster'] = true;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = '--usearch_fast_cluster';
		$input['--percent_id_err'] = $this->defaultValue;
		$input['--minsize'] = $this->defaultValue;
		$input['--abundance_skew'] = $this->defaultValue;
		$input['--db_filepath'] = true;
		$input['--perc_id_blast'] = $this->defaultValue;
		$input['--suppress_de_novo_chimera_detection'] = true;
		$input['--suppress_reference_chimera_detection'] = true;
		$input['--suppress_cluster_size_filtering'] = true;
		$input['--remove_usearch_logs'] = true;
		$input['--derep_fullseq'] = true;
		$input['--non_chimeras_retention'] = "union";
		$input['--minlen'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_usearch_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --similarity can only be used when:<br/>&nbsp;- --otu_picking_method is set to cdhit<br/>&nbsp;- --otu_picking_method is set to blast" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --word_length can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter __--usearch_fast_cluster__--enable_rev_strand_match__ can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --percent_id_err can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --minsize can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --abundance_skew can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --db_filepath can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --perc_id_blast can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_de_novo_chimera_detection can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_reference_chimera_detection can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_cluster_size_filtering can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --remove_usearch_logs can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --derep_fullseq can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --non_chimeras_retention can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --minlen can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "usearch";
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--usearch_fast_cluster'] = true;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = '--usearch_fast_cluster';
		$input['--percent_id_err'] = $this->defaultValue;
		$input['--minsize'] = $this->defaultValue;
		$input['--abundance_skew'] = $this->defaultValue;
		$input['--db_filepath'] = true;
		$input['--perc_id_blast'] = $this->defaultValue;
		$input['--suppress_de_novo_chimera_detection'] = true;
		$input['--suppress_reference_chimera_detection'] = true;
		$input['--suppress_cluster_size_filtering'] = true;
		$input['--remove_usearch_logs'] = true;
		$input['--derep_fullseq'] = true;
		$input['--non_chimeras_retention'] = "union";
		$input['--minlen'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_usearchRef_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>Since __--usearch_fast_cluster__--enable_rev_strand_match__ is set to --usearch_fast_cluster, --enable_rev_strand_match is not allowed.</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "usearch_ref";
		$input['--refseqs_fp'] = true;
		$input['--suppress_new_clusters'] = true;
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--usearch_fast_cluster'] = true;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = '--usearch_fast_cluster';
		$input['--percent_id_err'] = $this->defaultValue;
		$input['--minsize'] = $this->defaultValue;
		$input['--abundance_skew'] = $this->defaultValue;
		$input['--db_filepath'] = true;
		$input['--perc_id_blast'] = $this->defaultValue;
		$input['--suppress_de_novo_chimera_detection'] = true;
		$input['--suppress_reference_chimera_detection'] = true;
		$input['--suppress_cluster_size_filtering'] = true;
		$input['--remove_usearch_logs'] = true;
		$input['--derep_fullseq'] = true;
		$input['--non_chimeras_retention'] = "union";
		$input['--minlen'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_usearchRef_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --refseqs_fp can only be used when:<br/>&nbsp;- --otu_picking_method is set to blast<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch_ref<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_new_clusters can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch_ref<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --similarity can only be used when:<br/>&nbsp;- --otu_picking_method is set to cdhit<br/>&nbsp;- --otu_picking_method is set to blast" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --word_length can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter __--usearch_fast_cluster__--enable_rev_strand_match__ can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --percent_id_err can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --minsize can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --abundance_skew can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --db_filepath can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --perc_id_blast can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_de_novo_chimera_detection can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_reference_chimera_detection can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_cluster_size_filtering can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --remove_usearch_logs can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --derep_fullseq can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --non_chimeras_retention can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --minlen can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "usearch_ref";
		$input['--refseqs_fp'] = true;
		$input['--suppress_new_clusters'] = true;
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--usearch_fast_cluster'] = true;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = '--usearch_fast_cluster';
		$input['--percent_id_err'] = $this->defaultValue;
		$input['--minsize'] = $this->defaultValue;
		$input['--abundance_skew'] = $this->defaultValue;
		$input['--db_filepath'] = true;
		$input['--perc_id_blast'] = $this->defaultValue;
		$input['--suppress_de_novo_chimera_detection'] = true;
		$input['--suppress_reference_chimera_detection'] = true;
		$input['--suppress_cluster_size_filtering'] = true;
		$input['--remove_usearch_logs'] = true;
		$input['--derep_fullseq'] = true;
		$input['--non_chimeras_retention'] = "union";
		$input['--minlen'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_usearch61_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>Since __--usearch_fast_cluster__--enable_rev_strand_match__ is set to --usearch_fast_cluster, --enable_rev_strand_match is not allowed.</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "usearch61";
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--max_accepts'] = $this->defaultValue;
		$input['--max_rejects'] = $this->defaultValue;
		$input['--usearch_fast_cluster'] = true;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = '--usearch_fast_cluster';
		$input['--percent_id_err'] = $this->defaultValue;
		$input['--minsize'] = $this->defaultValue;
		$input['--abundance_skew'] = $this->defaultValue;
		$input['--db_filepath'] = true;
		$input['--perc_id_blast'] = $this->defaultValue;
		$input['--suppress_de_novo_chimera_detection'] = true;
		$input['--suppress_reference_chimera_detection'] = true;
		$input['--suppress_cluster_size_filtering'] = true;
		$input['--remove_usearch_logs'] = true;
		$input['--derep_fullseq'] = true;
		$input['--non_chimeras_retention'] = "union";
		$input['--minlen'] = $this->defaultValue;
		$input['--usearch61_sort_method'] = "abundance";
		$input['--threads'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_usearch61_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --similarity can only be used when:<br/>&nbsp;- --otu_picking_method is set to cdhit<br/>&nbsp;- --otu_picking_method is set to blast" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --word_length can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --max_accepts can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --max_rejects can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter __--usearch_fast_cluster__--enable_rev_strand_match__ can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --percent_id_err can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --minsize can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --abundance_skew can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --db_filepath can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --perc_id_blast can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_de_novo_chimera_detection can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_reference_chimera_detection can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_cluster_size_filtering can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --remove_usearch_logs can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --derep_fullseq can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --non_chimeras_retention can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --minlen can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --usearch61_sort_method can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --threads can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch61" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "usearch61";
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--max_accepts'] = $this->defaultValue;
		$input['--max_rejects'] = $this->defaultValue;
		$input['--usearch_fast_cluster'] = true;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = '--usearch_fast_cluster';
		$input['--percent_id_err'] = $this->defaultValue;
		$input['--minsize'] = $this->defaultValue;
		$input['--abundance_skew'] = $this->defaultValue;
		$input['--db_filepath'] = true;
		$input['--perc_id_blast'] = $this->defaultValue;
		$input['--suppress_de_novo_chimera_detection'] = true;
		$input['--suppress_reference_chimera_detection'] = true;
		$input['--suppress_cluster_size_filtering'] = true;
		$input['--remove_usearch_logs'] = true;
		$input['--derep_fullseq'] = true;
		$input['--non_chimeras_retention'] = "union";
		$input['--minlen'] = $this->defaultValue;
		$input['--usearch61_sort_method'] = "abundance";
		$input['--threads'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_usearch61Ref_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>Since __--usearch_fast_cluster__--enable_rev_strand_match__ is set to --usearch_fast_cluster, --enable_rev_strand_match is not allowed.</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "usearch61_ref";
		$input['--refseqs_fp'] = true;
		$input['--suppress_new_clusters'] = true;
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--max_accepts'] = $this->defaultValue;
		$input['--max_rejects'] = $this->defaultValue;
		$input['--usearch_fast_cluster'] = true;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = '--usearch_fast_cluster';
		$input['--percent_id_err'] = $this->defaultValue;
		$input['--minsize'] = $this->defaultValue;
		$input['--abundance_skew'] = $this->defaultValue;
		$input['--db_filepath'] = true;
		$input['--perc_id_blast'] = $this->defaultValue;
		$input['--suppress_de_novo_chimera_detection'] = true;
		$input['--suppress_reference_chimera_detection'] = true;
		$input['--suppress_cluster_size_filtering'] = true;
		$input['--remove_usearch_logs'] = true;
		$input['--derep_fullseq'] = true;
		$input['--non_chimeras_retention'] = "union";
		$input['--minlen'] = $this->defaultValue;
		$input['--usearch61_sort_method'] = "abundance";
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_usearch61Ref_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --refseqs_fp can only be used when:<br/>&nbsp;- --otu_picking_method is set to blast<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch_ref<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_new_clusters can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch_ref<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --similarity can only be used when:<br/>&nbsp;- --otu_picking_method is set to cdhit<br/>&nbsp;- --otu_picking_method is set to blast" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"</li>" .
			"<li>The parameter --word_length can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --max_accepts can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --max_rejects can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter __--usearch_fast_cluster__--enable_rev_strand_match__ can only be used when:<br/>&nbsp;- --otu_picking_method is set to uclust<br/>&nbsp;- --otu_picking_method is set to uclust_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --percent_id_err can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --minsize can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --abundance_skew can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --db_filepath can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --perc_id_blast can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_de_novo_chimera_detection can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_reference_chimera_detection can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --suppress_cluster_size_filtering can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --remove_usearch_logs can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --derep_fullseq can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --non_chimeras_retention can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --minlen can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			"<li>The parameter --usearch61_sort_method can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "usearch61_ref";
		$input['--refseqs_fp'] = true;
		$input['--suppress_new_clusters'] = true;
		$input['--similarity'] = $this->defaultValue;
		$input['--word_length'] = $this->defaultValue;
		$input['--max_accepts'] = $this->defaultValue;
		$input['--max_rejects'] = $this->defaultValue;
		$input['--usearch_fast_cluster'] = true;
		$input['--enable_rev_strand_match'] = true;
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = '--usearch_fast_cluster';
		$input['--percent_id_err'] = $this->defaultValue;
		$input['--minsize'] = $this->defaultValue;
		$input['--abundance_skew'] = $this->defaultValue;
		$input['--db_filepath'] = true;
		$input['--perc_id_blast'] = $this->defaultValue;
		$input['--suppress_de_novo_chimera_detection'] = true;
		$input['--suppress_reference_chimera_detection'] = true;
		$input['--suppress_cluster_size_filtering'] = true;
		$input['--remove_usearch_logs'] = true;
		$input['--derep_fullseq'] = true;
		$input['--non_chimeras_retention'] = "union";
		$input['--minlen'] = $this->defaultValue;
		$input['--usearch61_sort_method'] = "abundance";
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_prefixSuffix_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "prefix_suffix";
		$input['--prefix_length'] = $this->defaultValue;
		$input['--suffix_length'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_prefixSuffix_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --prefix_length can only be used when:<br/>&nbsp;- --otu_picking_method is set to prefix_suffix" .
				"</li>" .
			"<li>The parameter --suffix_length can only be used when:<br/>&nbsp;- --otu_picking_method is set to prefix_suffix" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "prefix_suffix";
		$input['--prefix_length'] = $this->defaultValue;
		$input['--suffix_length'] = $this->defaultValue;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_trie_triggerIsOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "trie";
		$input['--trie_reverse_seqs'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testOtuPickingMethod_trie_triggerIsNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>An invalid value was provided for the parameter: --otu_picking_method</li>" .
			"<li>The parameter --trie_reverse_seqs can only be used when:<br/>&nbsp;- --otu_picking_method is set to trie" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "not_" . "trie";
		$input['--trie_reverse_seqs'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testUsearch61SortMethod_triggerOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required" .
			"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "usearch61";
		$input['--usearch61_sort_method'] = "abundance";
		$input['--sizeorder'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testUsearch61SortMethod_triggerNotOnValue() {
		$expected = $this->errorMessageIntro .
			"<li>The parameter --input_seqs_filepath is required" .
			"</li>" .
			"<li>An invalid value was provided for the parameter: --usearch61_sort_method" .
			"</li>" .
			"<li>The parameter --sizeorder can only be used when:<br/>&nbsp;- --usearch61_sort_method is set to abundance" .
			"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "usearch61";
		$input['--usearch61_sort_method'] = "not_" . "abundance";
		$input['--sizeorder'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}

	public function testFastOrThoroughUsearchClustering_usearchFastClusterAllowed() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --input_seqs_filepath is required" .
			"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "usearch";
		$input['__--usearch_fast_cluster__--enable_rev_strand_match'] = "--usearch_fast_cluster";
		$input['--usearch_fast_cluster'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
	public function testFastOrThoroughUsearchClustering_usearchFastClusterNotAllowed() {
		$expected = $this->errorMessageIntro . 
			"<li>The parameter --input_seqs_filepath is required</li>" .
			"<li>The parameter --usearch_fast_cluster can only be used when:<br/>&nbsp;- --otu_picking_method is set to usearch<br/>&nbsp;- --otu_picking_method is set to usearch_ref" .
				"<br/>&nbsp;- --otu_picking_method is set to usearch61<br/>&nbsp;- --otu_picking_method is set to usearch61_ref" .
				"</li>" .
			$this->errorMessageOutro;
		$actual = "";
		$input = array();
		$input['--otu_picking_method'] = "uclust";
		$input['__--usearch_fast_cluster__--enable_rev_strand_match__'] = "--usearch_fast_cluster";
		$input['--usearch_fast_cluster'] = true;
		try {

			$this->object->acceptInput($input);

		}
		catch(ScriptException $ex) {
			$actual = $ex->getMessage();
		}
		$this->assertEquals($expected, $actual);
	}
}
