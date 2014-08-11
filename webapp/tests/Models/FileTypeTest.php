<?php

namespace Models;

class FileTypeTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("FileTypeTest");
	}

	private $arbitraryTextFileType = NULL;
	private $fastqFileType = NULL;
	private $mapFileType = NULL;
	private $sequenceFileType = NULL;
	private $sequenceQualityFileType = NULL;
	public function __construct($name = null, array $data = array(), $dataName = '')  {
		parent::__construct($name, $data, $dataName);
		
		$this->arbitraryTextFileType = new ArbitraryTextFileType();
		$this->fastqFileType = new FastqFileType();
		$this->mapFileType = new MapFileType();
		$this->sequenceFileType = new SequenceFileType();
		$this->sequenceQualityFileType = new SequenceQualityFileType();
	}

	/**
	 * @covers \Models\FileType::renderHelp
	 */
	public function testRenderHelp_examplePresent() {
		$expected = "<h4>name Files</h4>
			<p>help</p>";
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->setMethods(array("getName", "getExample", "getHelp"))
			->getMockForAbstractClass();
		$mockFileType->expects($this->once())->method("getName")->will($this->returnValue("name"));
		$mockFileType->expects($this->once())->method("getHelp")->will($this->returnValue("help"));
		$mockFileType->expects($this->once())->method("getExample")->will($this->returnValue(false));

		$actual = $mockFileType->renderHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\FileType::renderHelp
	 */
	public function testRenderHelp_noExamplePresent() {
		$expected = "<h4>name Files</h4>
			<p>help</p><div class=\"file_example\">example</div>";
		$mockFileType = $this->getMockBuilder('\Models\FileType')
			->setMethods(array("getName", "getExample", "getHelp"))
			->getMockForAbstractClass();
		$mockFileType->expects($this->once())->method("getName")->will($this->returnValue("name"));
		$mockFileType->expects($this->once())->method("getHelp")->will($this->returnValue("help"));
		$mockFileType->expects($this->exactly(2))->method("getExample")->will($this->returnValue("example"));

		$actual = $mockFileType->renderHelp();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\ArbitraryTextFileType::getName
	 */
	public function testArbitraryText_getName() {
		$expected = "Arbitrary Text";

		$actual = $this->arbitraryTextFileType->getName();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\ArbitraryTextFileType::getId
	 */
	public function testArbitraryText_getHtmlId() {
		$expected = "arbitrary_text";

		$actual = $this->arbitraryTextFileType->getHtmlId();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\ArbitraryTextFileType::getHelp
	 */
	public function testArbitraryText_getHelp() {
		$expected = "<p>Sometimes, there isn't a specific file type that fits the file you want to upload.  It may be required only once, in a highly specific context, and so your workflow
			doesn't usually call for it.  In that case, you can upload an arbitrary text file.  The program won't perform any format checking on it, but if it's necessary, you
			can upload it.</p>";

		$actual = $this->arbitraryTextFileType->getHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\ArbitraryTextFileType::getExample
	 */
	public function testArbitraryText_getExample() {
		$expected = "";

		$actual = $this->arbitraryTextFileType->getExample();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\FastqFileType::getName
	 */
	public function testFastq_getName() {
		$expected = "Fastq";

		$actual = $this->fastqFileType->getName();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\FastqFileType::getId
	 */
	public function testFastq_getHtmlId() {
		$expected = "fastq";

		$actual = $this->fastqFileType->getHtmlId();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\FastqFileType::getHelp
	 */
	public function testFastq_getHelp() {
		$expected = "A fastq file contains both sequence and quality information.
			It is the favored format for Illumina sequencing.
			Each entry (read) is four lines:<ol>
			<li>The identifier (begins with special character '@'</li>
			<li>The sequence (IUPAC nucleotide abbreviations)</li>
			<li>A second 'header' line, beginning with '+', that may contain a comment, the sequence header repeated, or nothing</li>
			<li>The quality scores*</li></ol>

			* Each individual character on the line in the file corresponds to one base in the read,
			so all the nonsense-looking punctuation this line is actually a series of ASCII codes.
			I. e.<ul><li>F means a quality of 70</li><li>= means a quality of 61</li><li>; means a quality of 59</li></ul>";

		$actual = $this->fastqFileType->getHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\FastqFileType::getExample
	 */
	public function testFastq_getExample() {
		$expected = "
@FLP3FBN01ELBSX
ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTCTGGGCCGTGTCTCAGTCCCAATGTGGCCGTTTACCCTCTCAGGCCGGCTACGCATCATCGCCTTGG
+
FFFFFF====FFFFFFFFFFEEBBBEFFFFFFIIIHHGIIIIIIIFFFFFDDDFFFFFDDD@@888@666DDFFFEEEEEEFFFFFFFFFFFFFFFFFFF
@FLP3FBN01EG8AX
ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTTTGGACCGTGTCTCAGTTCCAATGTGGGGGCCTTCCTCTCAGAACCCCTATCCATCGAAGGCTTGGT
+
FFFFFFFFFFFFFFFFFFGFBB666;BFEEIB99>BBHHHIHHHIFFFFFFFFFFFAA55555DDFFFFFFFEEEFFFFFFFFFFFFFFFFFFFFFFFFF
@FLP3FBN01EEWKD
AGCACGAGCCTACATGCTGCCTCCCGTAGGAGTTTGGGCCGTGTCTCAGTCCCAATGTGGCCGATCAGTCTCTTAACTCGGCTATGCATCATTGCCTTGG
+
EFFFFFFFFFFFFFFFFFEEBB;66@EFFFEEBCCEFEFFFFFFFFFFFE===EEFFDDDFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFA";

		$actual = $this->fastqFileType->getExample();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\MapFileType::getName
	 */
	public function testMap_getName() {
		$expected = "Map";

		$actual = $this->mapFileType->getName();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\MapFileType::getId
	 */
	public function testMap_getHtmlId() {
		$expected = "map";

		$actual = $this->mapFileType->getHtmlId();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\MapFileType::getHelp
	 */
	public function testMap_getHelp() {
		$expected = "
			<p>A map file contains metadata about your samples.  It is tab-delineated text, formatted in a table, with one sample per row, one characteristic of the sample per column.</p>
			<p>The example below contains the four required fields: a name for each sample, its unique barcode, the linker/primer used to amplify the sample, and a description.
			Additionally, you can include information such as case/control status and potential lurking variables that could affect one or more of the samples.
			If necessary, you can leave out sequences for the barcode and/or primer.  You need to leave the header, though, and a blank column where the sequence would be.</p>";

		$actual = $this->mapFileType->getHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\MapFileType::getExample
	 */
	public function testMap_getExample() {
		$expected = "#SampleID	BarcodeSequence	LinkerPrimerSequence	Description
PC.354	AGCACGAGCCTA	YATGCTGCCTCCCGTAGGAGT	20061218	Control_mouse_I.D._354
PC.355	AACTCGTCGATG	YATGCTGCCTCCCGTAGGAGT	20061218	Control_mouse_I.D._355
PC.356	ACAGACCACTCA	YATGCTGCCTCCCGTAGGAGT	20061126	Control_mouse_I.D._356
PC.481	ACCAGCGACTAG	YATGCTGCCTCCCGTAGGAGT	20070314	Control_mouse_I.D._481";

		$actual = $this->mapFileType->getExample();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\SequenceFileType::getName
	 */
	public function testSequence_getName() {
		$expected = "Sequence";

		$actual = $this->sequenceFileType->getName();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\SequenceFileType::getId
	 */
	public function testSequence_getHtmlId() {
		$expected = "sequence";

		$actual = $this->sequenceFileType->getHtmlId();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\SequenceFileType::getHelp
	 */
	public function testSequence_getHelp() {
		$expected = "Fasta files have header lines for sequence identifying informaiton (they begin with a &qt;&gt;&qt; symbol), and lines with the sequences themselves.</p>";

		$actual = $this->sequenceFileType->getHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\SequenceFileType::getExample
	 */
	public function testSequence_getExample() {
		$expected = "&gt;FLP3FBN01ELBSX length=250 xy=1766_0111 region=1 run=R_2008_12_09_13_51_01_
ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTCTTAGCTAATGCGCCGCAGGTCCATCCATGTTCACGCCTTGATGGGCGCT
&gt;FLP3FBN01EG8AX length=276 xy=1719_1463 region=1 run=R_2008_12_09_13_51_01_
ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTTTGGACCGTGTCTCAGTTCCAATGTGGGGGCTTGGTGGGCCGTTACCCCGCCAACA";

		$actual = $this->sequenceFileType->getExample();

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @covers \Models\SequenceQualityFileType::getName
	 */
	public function testSequenceQuality_getName() {
		$expected = "Sequence Quality";

		$actual = $this->sequenceQualityFileType->getName();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\SequenceQualityFileType::getId
	 */
	public function testSequenceQuality_getHtmlId() {
		$expected = "quality";

		$actual = $this->sequenceQualityFileType->getHtmlId();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\SequenceQualityFileType::getHelp
	 */
	public function testSequenceQuality_getHelp() {
		$expected = "<p>A sequence quality file is matched to a sequence file, and a pair must be uploaded at the same time.
			The format is roughly parallel, but instead of unseparated bases designations, the quality file has space-delineated quality scores.</p>";

		$actual = $this->sequenceQualityFileType->getHelp();

		$this->assertEquals($expected, $actual);
	}
	/**
	 * @covers \Models\SequenceQualityFileType::getExample
	 */
	public function testSequenceQuality_getExample() {
		$expected = "&gt;FLP3FBN01ELBSX length=250 xy=1766_0111 region=1 run=R_2008_12_09_13_51_01_
37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 36 36 33 33 33 36 37 37 37 37 37
&gt;FLP3FBN01EG8AX length=276 xy=1719_1463 region=1 run=R_2008_12_09_13_51_01_
37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 38 37 33 33 21 21 21 26 33 37 36 36 40 33 24 24 29 33";

		$actual = $this->sequenceQualityFileType->getExample();

		$this->assertEquals($expected, $actual);
	}
}
