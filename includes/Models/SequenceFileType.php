<?php

namespace Models;

class SequenceFileType extends FileType {
	public function getName() {
		return "Sequence";
	}
	public function getHtmlId() {
		return "sequence";
	}
	public function getHelp() {
		return "Fasta files have header lines for sequence identifying informaiton (they begin with a &qt;&gt;&qt; symbol), and lines with the sequences themselves.</p>";
	}
	public function getExample() {
		return "&gt;FLP3FBN01ELBSX length=250 xy=1766_0111 region=1 run=R_2008_12_09_13_51_01_
ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTCTTAGCTAATGCGCCGCAGGTCCATCCATGTTCACGCCTTGATGGGCGCT
&gt;FLP3FBN01EG8AX length=276 xy=1719_1463 region=1 run=R_2008_12_09_13_51_01_
ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTTTGGACCGTGTCTCAGTTCCAATGTGGGGGCTTGGTGGGCCGTTACCCCGCCAACA";
	}
}
