<?php

namespace Models;

class SequenceQualityFileType extends FileType {
	public function __construct() {
		$this->name = "Sequence Quality";
		$this->htmlId = "quality";
		$this->help = "<p>A sequence quality file is matched to a sequence file, and a pair must be uploaded at the same time.
			The format is roughly parallel, but instead of unseparated bases designations, the quality file has space-delineated quality scores.</p>";
		$this->example = "&gt;FLP3FBN01ELBSX length=250 xy=1766_0111 region=1 run=R_2008_12_09_13_51_01_
37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 36 36 33 33 33 36 37 37 37 37 37
&gt;FLP3FBN01EG8AX length=276 xy=1719_1463 region=1 run=R_2008_12_09_13_51_01_
37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 38 37 33 33 21 21 21 26 33 37 36 36 40 33 24 24 29 33";
	}
}
