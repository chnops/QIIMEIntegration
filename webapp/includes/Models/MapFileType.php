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
		$this->example = "<table>
				<tr><td>#SampleID</td><td>BarcodeSequence</td><td>LinkerPrimerSequence</td><td>Description</td></tr>
				<tr><td>PC.354</td><td>AGCACGAGCCTA</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>20061218</td><td>Control_mouse_I.D._354<td></tr>
				<tr><td>PC.355</td><td>AACTCGTCGATG</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>20061218</td><td>Control_mouse_I.D._355<td></tr>
				<tr><td>PC.356</td><td>ACAGACCACTCA</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>20061126</td><td>Control_mouse_I.D._356<td></tr>
				<tr><td>PC.481</td><td>ACCAGCGACTAG</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>20070314</td><td>Control_mouse_I.D._481<td></tr>
				</table>";
	}
}
