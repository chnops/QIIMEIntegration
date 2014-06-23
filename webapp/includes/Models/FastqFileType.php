<?php

namespace Models;

class FastqFileType extends FileType {
	/*protected $name;
	protected $htmlId;
	protected $help;
	protected $example;*/

	public function __construct() {
		$this->name = "Fastq";
		$this->htmlId = "fastq";
		$this->help = "A fastq file contains both sequence and quality information.
			It is the favored format for Illumina sequencing.
			Each entry (read) is four lines:<ol>
			<li>The identifier (begins with special character '@'</li>
			<li>The sequence (IUPAC nucleotide abbreviations)</li>
			<li>A second 'header' line, beginning with '+', that may contain a comment, the sequence header repeated, or nothing</li>
			<li>The quality scores*</li></ol>

			* Each individual character on the line in the file corresponds to one base in the read,
			so all the nonsense-looking punctuation this line is actually a series of ASCII codes.
			I. e.<ul><li>F means a quality of 70</li><li>= means a quality of 61</li><li>; means a quality of 59</li></ul>";

		$this->example = "
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
	}

	public function renderHelp() {
		$output = "<h4>{$this->name} Files</h4>
			<p>{$this->help}</p>";
		if ($this->example) {
			$output .= "<div class=\"file_example\">{$this->example}</div>";
		}
		return $output;
	}
}
