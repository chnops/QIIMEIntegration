<?php

class UploadController extends Controller {

	protected $subTitle = "Upload a Map";
	protected $content = "
<h2>Upload a map file</h2>
<p>A map file contains metadata about your samples.  It is tab-delineated text, formatted in a table, with one sample per row, one characteristic of the sample per column. For example:
	<div class=\"file_example\">
	<table>
	<tr><td>#SampleID</td><td>BarcodeSequence</td><td>LinkerPrimerSequence</td><td>Description</td></tr>
	<tr><td>PC.354</td><td>AGCACGAGCCTA</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>Control</td><td>20061218</td><td>Control_mouse_I.D._354<td></tr>
	<tr><td>PC.355</td><td>AACTCGTCGATG</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>Control</td><td>20061218</td><td>Control_mouse_I.D._355<td></tr>
	<tr><td>PC.356</td><td>ACAGACCACTCA</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>Control</td><td>20061126</td><td>Control_mouse_I.D._356<td></tr>
	<tr><td>PC.481</td><td>ACCAGCGACTAG</td><td>YATGCTGCCTCCCGTAGGAGT</td><td>Control</td><td>20070314</td><td>Control_mouse_I.D._481<td></tr>
	</table>
	</div></p>

<p>The example above contains the four required fields: a name for each sample, its unique barcode, the linker/primer used to amplify the sample, and a description.
Additionally, you can include information such as case/control status and potential lurking variables that could affect one or more of the samples.
If necessary, you can leave out sequences for the barcode and/or primer.  You need to leave the header, though, and a blank column where the sequence would be.</p>

<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
	<input type=\"hidden\" name=\"page\" value=\"step1.php\"/>
	<label for=\"file\">Select a file to upload:<br/>
	<input type=\"file\" name=\"map_file\"/></label><br/>
	<button type=\"submit\">Upload</button>
	</form>
<h2>Upload additional files</h2>
<p>Here you can upload sequences, quality scores, and .sff files to enhance your analysis</p>
<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">
	<input type=\"hidden\" name=\"page\" value=\"step2.php\"/>
	<label for=\"file\">Select a sequence file to upload*:<br/>
	<input type=\"file\" name=\"sequence_file\"/></label><br/>
	<label for=\"file\">Select a quality score file to upload:<br/>
	<input type=\"file\" name=\"quality_file\"/></label><br/>
	<label for=\"file\">Select a .sff file to upload:<br/>
	<input type=\"file\" name=\"sff_file\"/></label><br/>
	<button type=\"submit\">Upload</button>
	<p class=\"footnote\">* Required file</p>
</form>";
	protected $help = "<a href=\"index.php?step=make_otu\">Go to next step</a>";
	protected $step = 'upload';
	private $fileName = "";

	public function parseInput() {
		if (isset($_FILES['map_file'])) {
			echo $this->uploadFile($_FILES['map_file']);
		}	
		if (isset($_FILES['sequence_file'])) {
			echo $this->uploadFile($_FILES['sequence_file']) . "<br/>";
		}
		if (isset($_FILES['quality_file'])) {
			echo "Sorry, quality files not implemented yet.<br/>";
		}
		if (isset($_FILES['sff_file'])) {
			echo "Sorry, .sff files not implemented yet.<br/>";
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
}
