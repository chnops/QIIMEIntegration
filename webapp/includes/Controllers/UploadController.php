<?php

namespace Controllers;

class UploadController extends Controller {

	protected $subTitle = "Upload a Map";

	public function retrievePastResults() {
		return "Not yet implement";
	}

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->isResultError = true;
			$this->hasResult = true;
			$this->result = "In order to upload files, you must be logged in and have a project selected.";
			return;
		}
		if (isset($_FILES['map_file'])) {
			echo $this->uploadFile($_FILES['map_file']);
		}	
		if (isset($_FILES['sequence_file'])) {
			echo $this->uploadFile($_FILES['sequence_file']) . "<br/>";
		}
		if (isset($_FILES['quality_file'])) {
			echo "Sorry, quality files not implemented yet.<br/>";
		}
	}

	private function uploadFile(array $file) {
		if ($file['error'] > 0) {
			return "There was a problem uploading your file.";
		}
		if (!strstr($file['type'], "text/")) {
			return "You may only upload a text file.";
		}
		if ($file['size'] > 1024 * 24) {
			return "That file is too large to upload.  Please upload a file less than 24 Kb.";
		}
		
		$result = move_uploaded_file($file['tmp_name'], "projects/" . $file['name']);
		if (!$result) {
			return "An unexpected error occurred while uploading your file.";
		}
		$_SESSION['map_file'] = $this->fileName = $file['name'];
		return "File successfully uploaded!";
	}

	/*public function renderOutput() {
		echo "<div class=\"loading_bar\">Loading... loading...</div>";
		echo "Just kidding, it ran already.<br/>";
		$fileName = $this->sanitizeFileName($this->fileName);
		system("source /macqiime/configs/bash_profile.txt; validate_mapping_file.py -s -o projects/mapping_output -m projects/{$fileName}");
	}*/

	public function sanitizeFileName($fileName) {
		return $fileName;
	}

	public function getInstructions() {
		return "<p>There are three types of files you can upload to start your project:
			<ol>
			<li>A map file</li>
			<li>A fasta formatted sequence file</li>
			<li>A sequence quality file</li>
			</ol>
			Tips on file formats can be found below.
			</p>
			<h4>Map files</h4>
			<p>A map file contains metadata about your samples.  It is tab-delineated text, formatted in a table, with one sample per row, one characteristic of the sample per column. For example:
			<div class=\"file_example\">
				<table>
				<tr><td>#SampleID</td><td>BarcodeSequence</td><td>LinkerPrimerSequence</td><td>Description</td></tr>
				<tr><td>PC.354</td><td>AGCACGAGCCTA</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>20061218</td><td>Control_mouse_I.D._354<td></tr>
				<tr><td>PC.355</td><td>AACTCGTCGATG</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>20061218</td><td>Control_mouse_I.D._355<td></tr>
				<tr><td>PC.356</td><td>ACAGACCACTCA</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>20061126</td><td>Control_mouse_I.D._356<td></tr>
				<tr><td>PC.481</td><td>ACCAGCGACTAG</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>20070314</td><td>Control_mouse_I.D._481<td></tr>
				</table>
			</div></p>

			<p>The example above contains the four required fields: a name for each sample, its unique barcode, the linker/primer used to amplify the sample, and a description.
			Additionally, you can include information such as case/control status and potential lurking variables that could affect one or more of the samples.
			If necessary, you can leave out sequences for the barcode and/or primer.  You need to leave the header, though, and a blank column where the sequence would be.
			<h4>Sequence files</h4>
			Fasta files have header lines for sequence identifying informaiton (they begin with a &qt;&gt;&qt; symbol), and lines with the sequences themselves.</p>
			<div class=\"file_example\">
				&gt;FLP3FBN01ELBSX length=250 xy=1766_0111 region=1 run=R_2008_12_09_13_51_01_<br/>
				ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTCTGGGCCGTGTCTCAGTCCCAATGTGGCCGTTTACCCTCTCAGGCCGGCTACGCATCATCGCCTTGGTGGGCCGTTACCTCACCAAC<br/>
				TAGCTAATGCGCCGCAGGTCCATCCATGTTCACGCCTTGATGGGCGCTTTAATATACTGAGCATGCGCTCTGTATACCTATCCGGTTTTAGCTACCGTTTCCAGCAGTTATCCCGGACACATGGGCTAGG<br/>
				&gt;FLP3FBN01EG8AX length=276 xy=1719_1463 region=1 run=R_2008_12_09_13_51_01_<br/>
				ACAGAGTCGGCTCATGCTGCCTCCCGTAGGAGTTTGGACCGTGTCTCAGTTCCAATGTGGGGGCCTTCCTCTCAGAACCCCTATCCATCGAAGGCTTGGTGGGCCGTTACCCCGCCAACA<br/>
				ACCTAATGGAACGCATCCCCATCGATGACCGAAGTTCTTTAATAGTTCTACCATGCGGAAGAACTATGCCATCGGGTATTAATCTTTCTTTCGAAAGGCTATCCCCGAGTCATCGGCAGG<br/>
				TTGGATACGTGTTACTCACCCGTGCGCCGGTCGCCA<br/>
			</div>
			<h4>Sequence quality files</h4>
			<p>A sequence quality file is matched to a sequence file, and a pair must be uploaded at the same time.
			The format is roughly parallel, but instead of unseparated bases designations, the quality file has space-delineated quality scores.</p>
			<div class=\"file_example\">
				&gt;FLP3FBN01ELBSX length=250 xy=1766_0111 region=1 run=R_2008_12_09_13_51_01_<br/>
				37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 36 36 33 33 33 36 37 37 37 37 37 37 40 40 40 39 39 38 40 40 40 40 40 40 40 37 37 37 37 37 35 35 35 37 37 37 37 37 35 35
				35 31 31 23 23 23 31 21 21 21 35 35 37 37 37 36 36 36 36 36 36 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 28 28 28 36 36 37 37 37 37 37 37 37 37 37 37 37 37 37 37<br/>
				37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 36 36 36 37 37 37 37 37 37 37 37 37 37 37 37 36 36 36 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 35 32 32 32 32 35 37 37 37 37
				37 37 37 37 37 37 37 37 37 37 37 37 37 37 36 32 32 32 36 37 35 32 32 32 32 32 32 32 32 36 37 37 37 37 36 36 31 31 32 32 36 36 36 36 36 36 36 36 36 36 36 28 27 27 27 26 26 26 30 29<br/>
				30 29 24 24 24 21 15 15 13 13<br/>
				&gt;FLP3FBN01EG8AX length=276 xy=1719_1463 region=1 run=R_2008_12_09_13_51_01_<br/>
				37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 38 37 33 33 21 21 21 26 33 37 36 36 40 33 24 24 29 33 33 39 39 39 40 39 39 39 40 37 37 37 37 37 37 37 37 37 37 37 32 32 20 20
				20 20 20 35 35 37 37 37 37 37 37 37 36 36 36 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 36 36 36 36 36 36 37 37 37 37 37 36 36 36 36 37 37<br/>
				37 37 37 37 37 37 37 37 37 37 37 37 37 37 36 33 28 28 28 28 36 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 36 33 33 33 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37
				37 36 36 36 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 37 28 28 28 37 28 28 28 37 37 37 37 37 36 36 36 36 36 28 26 26 26 26 28 36 36 36 36 36 36 36 37 38 38 38 38 38<br/>
				37 37 37 37 37 31 31 31 31 31 31 31 31 31 31 31 31 30 22 22 22 25 25 31 31 31 31 31 31 31 25 25 25 25 25 28<br/>
			</div>
			";
	}
	public function getForm() {
		return "
			<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
				<input type=\"hidden\" name=\"step\" value=\"{$this->step}\"/>
				<label for=\"map_file\">Select a map file to upload:<br/>
				<input type=\"file\" name=\"map_file\"/></label>
				<hr class=\"small\"/>
				<label for=\"sequence_file\">Select a sequence file to upload:<br/>
				<input type=\"file\" name=\"sequence_file\"/></label>
				<hr class=\"small\"/>
				<label for=\"quality_file\">Select a quality score file to upload:<br/>
				<input type=\"file\" name=\"quality_file\"/></label>
				<label for=\"quality_is_associated\"><input type=\"checkbox\" checked name=\"quality_is_associated\" value=\"1\" disabled/>
				Quality file is associated with sequence file</label>
				<button type=\"submit\">Upload</button>
				</form>";
	}
}
