<?php

namespace Models;

class MapFileType extends FileType {
	public function __construct() {
		$this->name = "Map";
		$this->htmlId = "map";
		$this->help = "
			<p>A map file contains metadata about your samples.  It is tab-delineated text, formatted in a table, with one sample per row, one characteristic of the sample per column.</p>
			<p>The example below contains the four required fields: a name for each sample, its unique barcode, the linker/primer used to amplify the sample, and a description.
			Additionally, you can include information such as case/control status and potential lurking variables that could affect one or more of the samples.
			If necessary, you can leave out sequences for the barcode and/or primer.  You need to leave the header, though, and a blank column where the sequence would be.</p>";
		$this->example = "#SampleID	BarcodeSequence	LinkerPrimerSequence	Description
PC.354	AGCACGAGCCTA	YATGCTGCCTCCCGTAGGAGT	20061218	Control_mouse_I.D._354
PC.355	AACTCGTCGATG	YATGCTGCCTCCCGTAGGAGT	20061218	Control_mouse_I.D._355
PC.356	ACAGACCACTCA	YATGCTGCCTCCCGTAGGAGT	20061126	Control_mouse_I.D._356
PC.481	ACCAGCGACTAG	YATGCTGCCTCCCGTAGGAGT	20070314	Control_mouse_I.D._481";
	}
}
