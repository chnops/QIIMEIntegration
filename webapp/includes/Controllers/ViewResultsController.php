<?php

namespace Controllers;

class ViewResultsController extends Controller {

	public function getSubTitle() {
		return "View Results";
	}
	public function retrievePastResults() {
		if (!$this->project) {
			return "<p>In order to view results, you must <a href=\"?step=login\">log in</a> and <a href=\"?step=select\">select a project</a></p>";
		}

		$output = "<h3>" . $this->helper->htmlentities($this->project->getName()) . "</h3>";
		$output .= "<ul>
			<li>Owner:  " . $this->helper->htmlentities($this->project->getOwner()) . "</li>
			<li>Unique id: " . $this->helper->htmlentities($this->project->getId()) . "</li>
			</ul>";

		$output .= "<hr/>You can see a preview of the file you wish to download here:<br/>
			<div class=\"file_example\" id=\"file_preview\"></div>";

		return $output;
	}

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->isResultError = true;
			$this->result = "You have not selected a project, therefore there are no results to view.";
			return;
		}

		if (!isset($_POST['action'])) {
			return;
		}
		$action = $_POST['action'];
		$run = $_POST['run'];
		$file = $_POST['file'];
		
		if (!is_numeric($run)) {
			$this->isResulsError = true;
			$this->result = "Run id must be numeric";
		}
		$isUploaded = ($run == -1);

		$fileDisplay = $this->helper->htmlentities($file);

		if ($action == 'delete') {
			try {
				if ($isUploaded) {
					$this->project->deleteUploadedFile($file);
				}
				else {
					$this->project->deleteGeneratedFile($file, $run);
				}
				$this->result = "File deleted: " . $fileDisplay;
			}
			catch (\Exception $ex) {
				$this->isResultError = true;
				$this->result = $ex->getMessage();
			}
		}
		else if ($action == 'unzip') {
			try {
				if ($isUploaded) {
					$this->project->unzipUploadedFile($file);
				}
				else {
					$this->project->unzipGeneratedFile($file, $run);
				}
				$this->result = "Successfully unzipped file: " . $fileDisplay; 
			}
			catch (\Exception $ex) {
				if ($ex instanceof \Models\OperatingSystemException) {
					error_log($ex->getConsoleOutput());
				}
				$this->isResultError = true;
				$this->result = $ex->getMessage();
			}
		}
		else if ($action == 'gzip') {
			try {
				if ($isUploaded) {
					$this->project->compressUploadedFile($file);
				}
				else {
					$this->project->compressGeneratedFile($file, $run);
				}
				$this->result = "Successfully compressed file: " . $fileDisplay;
			}
			catch (\Exception $ex) {
				if ($ex instanceof \Models\OperatingSystemException) {
					error_log($ex->getConsoleOutput());
				}
				$this->isResultError = true;
				$this->result = $ex->getMessage();
			}
		}
		else if ($action == 'gunzip') {
			try {
				if ($isUploaded) {
					$this->project->decompressUploadedFile($file);
				}
				else {
					$this->project->decompressGeneratedFile($file, $run);
				}
				$this->result = "Successfully de-compressed file: " . $fileDisplay;
			}
			catch (\Exception $ex) {
				if ($ex instanceof \Models\OperatingSystemException) {
					error_log($ex->getConsoleOutput());
				}
				$this->isResultError = true;
				$this->result = $ex->getMessage();
			}
		}
	}

	public function renderInstructions() {
		return "";
	}
	public function renderForm() {
		if (!$this->project) {
			return "";
		}
		$output = "";	

		$uploadedFiles = $this->project->retrieveAllUploadedFiles();
		$rowHtmlId = 0;
		if (!empty($uploadedFiles)) {
			$output .= "<h3>Uploaded Files:</h3><div class=\"accordion\">\n";
			$uploadedFilesFormatted = $this->helper->categorizeArray($uploadedFiles, 'type'); 
			foreach ($uploadedFilesFormatted as $fileType => $files) {
				$output .= "<h4 onclick=\"hideMe($(this).next())\">{$fileType} files</h4><div><table>\n";
				foreach ($files as $file) {
					$output .= $this->renderFileMenu($rowHtmlId, $file['name'], $file['status'], $file['size']);
					$rowHtmlId++;
				}
				$output .= "</table></div>\n";
			}
			$output .= "</div>\n";
		}
		
		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($generatedFiles)) {
			$output .= "<h3>Generated Files:</h3><div class=\"accordion\">\n";
			$generatedFilesFormatted = $this->helper->categorizeArray($generatedFiles, 'run_id');
			foreach ($generatedFilesFormatted as $runId => $files) {
				$output .= "<h4 onclick=\"hideMe($(this).next())\">files from run {$runId}</h4><div><table>\n";
				foreach ($files as $file) {
					$output .= $this->renderFileMenu($rowHtmlId, $file['name'], 'ready', -1, $file['run_id']);
					$rowHtmlId++;
				}
				$output .= "</table></div>\n";
			}
			$output .= "</div>";
		}

		return $output;
	}
	private function renderFileMenu($rowHtmlId, $fileName, $fileStatus, $fileSize, $runId = -1) {
		$downloadLink = "download.php?file_name={$fileName}&run={$runId}";

		$sizeDisclaimer = ($fileSize && $fileSize >= 0) ? "<em>size: {$fileSize}B</em>" : "<em>size uncertain</em>";

		$row = "<tr class=\"{$fileStatus}\" id=\"result_file_{$rowHtmlId}\"><td>" . $this->helper->htmlentities($fileName) . " ({$fileStatus}) ({$sizeDisclaimer})</td>
			<td><a class=\"button\" onclick=\"previewFile('{$downloadLink}&as_text=true')\">Preview</a></td>
			<td><a class=\"button\" onclick=\"window.location='{$downloadLink}'\">Download</a></td>
			<td><a class=\"button more\" onclick=\"$(this).parents('tr').next().toggle('highlight', {}, 500);$(this).parents('tr').next().next().toggle('highlight', {}, 500);\">More...</a></td></tr>";

		$fileTypeInput = "<input type=\"hidden\" name=\"run\" value=\"{$runId}\">";
		$fileNameInput = "<input type=\"hidden\" name=\"file\" value=\"{$fileName}\">";
		$genericForm = "<td><form action=\"#result_file_{$rowHtmlId}\" method=\"POST\" %s>%s{$fileTypeInput} {$fileNameInput}<input type=\"submit\" name=\"action\" value=\"%s\"></form></td>";

		$row .= "<tr><td>&nbsp;</td>";
		$row .= $deleteForm = sprintf($genericForm, $jScript = "onsubmit=\"return confirm('Are you sure you want to delete this file? Action cannot be undone');\"",
			$extraInput = "", $action = "delete");
		$row .= $compressForm = sprintf($genericForm, $jScript = "", $extraInput = "", $action = "gzip");
		$row .= $unzipForm = sprintf($genericForm, $jScript = "", $extraInput = "", $action = "unzip");
		$row .= "</tr><tr><td>&nbsp;</td><td>&nbsp;</td>";
		$row .= $deCompressForm = sprintf($genericForm, $jScript = "", $extraInput = "", $action = "gunzip");
		$row .= "<td>&nbsp;</td></tr>";

		return $row;
	}
	public function renderHelp() {
		return "<p>Here is the moment you've been waiting for... your results! From this page, you can preview, download, and manage any of the files that
			you have uploaded or generated by running scripts.</p>";
	}

	public function renderSpecificStyle() {
		return "div#file_preview{margin:.75em;display:none}
			div.form table{border-collapse:collapse;margin:0px;width:100%}
			div.form td{padding:.5em;white-space:nowrap}
			div.form tr{background-color:#FFF6B2}
			div.form tr:nth-child(6n+1){background-color:#FFFFE0}
			div.form tr:nth-child(6n+2){background-color:#FFFFE0}
			div.form tr:nth-child(6n+3){background-color:#FFFFE0}
			div.form button{padding:.25em;margin:.25em;font-size:.80em}";
	}
	public function renderSpecificScript() {
		return "function previewFile(url){
			var displayDiv = $('#file_preview');
			displayDiv.css('display', 'block');
			displayDiv.load(url);}
			$(function() {
				$('div.form td').each(function() {
					$(this).width($(this).width());
				});
				$('a.more').click();
				var hash = window.location.hash;
				if(hash) {
					$(hash + ' a.more').click();
				}
			});";
	}
	public function getScriptLibraries() {
		return array();
	}
}
