<?php

namespace Models;

class SequenceFileType extends FileType {
	public function __construct() {
		$this->name = "Sequence";
		$this->shortName = "sequence";
		$this->help = "Fasta files have header lines for sequence identifying informaiton (they begin with a &qt;&gt;&qt; symbol), and lines with the sequences themselves.</p>";
		$this->example = "
				&gt;FLP3FBN01ELBSX length=250 xy=1766_0111 region=1 run=R_2008_12_09_13_51_01_<br/>
				ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTCTTAGCTAATGCGCCGCAGGTCCATCCATGTTCACGCCTTGATGGGCGCT<br/>
				&gt;FLP3FBN01EG8AX length=276 xy=1719_1463 region=1 run=R_2008_12_09_13_51_01_<br/>
				ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTTTGGACCGTGTCTCAGTTCCAATGTGGGGGCTTGGTGGGCCGTTACCCCGCCAACA";
	}
}
