<?php

namespace Controllers;

class ViewResultsController extends Controller {

	public function getSubTitle() {
		return "View Results";
	}

	public function parseInput() {
		if (!$this->username || !$this->project) {
			$this->isResultError = true;
			$this->result = "You have not selected a project, therefore there are no results to view.";
			return;
		}

		// TODO add action input
		if (!isset($_POST['delete']) && !isset($_POST['unzip'])) {
			return;
		}

		$isUploaded = (isset($_POST['uploaded']) && $_POST['uploaded']);
		if (!$isUploaded) {
			if (!isset($_POST['run'])) {
				$this->isResultError = true;
				$this->result = "You must provide either a run id, or specify that the file is uploaded.";
			}
			if (!is_numeric($_POST['run'])) {
				$this->isResulsError = true;
				$this->result = "Run id must be numeric";
			}
		}

		if (isset($_POST['delete'])) {
			try {
				if ($isUploaded) {
					$this->project->deleteUploadedFile($_POST['delete']);
				}
				else {
					$this->project->deleteGeneratedFile($_POST['delete'], $_POST['run']);
				}
				$this->result = "File deleted: " . htmlentities($_POST['delete']);
			}
			catch (\Exception $ex) {
				$this->isResultError = true;
				$this->result = $ex->getMessage();
			}
		}
		else if (isset($_POST['unzip'])) {
			try {
				if ($isUploaded) {
					$this->project->unzipUploadedFile($_POST['unzip']);
				}
				else {
					$this->project->unzipGeneratedFile($_POST['unzip'], $_POST['run']);
				}
				$this->result = "Successfully unzipped file: " . htmlentities($_POST['unzip']);
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
	public function retrievePastResults() {
		if (!$this->project) {
			return "<p>In order to view results, you much <a href=\"\">log ing</a> and <a href=\"\">select a project</a></p>";
		}

		$output = "<h3>{$this->project->getName()}</h3>";
		$output .= "<ul>
			<li>Owner: {$this->project->getOwner()}</li>
			<li>Unique id: {$this->project->getId()}</li>
			</ul>";

		$uploadedFiles = $this->project->retrieveAllUploadedFiles();
		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($uploadedFiles) || !empty($generatedFiles)) {
			$output .= "<hr/>You can see a preview of the file you wish to download here:<br/>
				<div class=\"file_example\" id=\"file_preview\"></div>";
		}

		return $output;
	}
	public function renderInstructions() {
		return "";
	}
	public function renderForm() {
		if (!$this->project) {
			return "";
		}
		$output = "";	

		$helper = \Utils\Helper::getHelper();
		$uploadedFiles = $this->project->retrieveAllUploadedFiles();
		$rowHtmlId = 0;
		if (!empty($uploadedFiles)) {
			$output .= "<h3>Uploaded Files:</h3><div class=\"accordion\">\n";
			$uploadedFilesFormatted = $helper->categorizeArray($uploadedFiles, 'type'); 
			foreach ($uploadedFilesFormatted as $fileType => $files) {
				$output .= "<h4 onclick=\"hideMe($(this).next())\">{$fileType} files</h4><div><table>\n";
				foreach ($files as $file) {
					$output .= $this->renderFileMenu($rowHtmlId, $file['name'], $file['status'], $isUploaded = true);
					$rowHtmlId++;
				}
				$output .= "</table></div>\n";
			}
			$output .= "</div>\n";
		}
		
		$generatedFiles = $this->project->retrieveAllGeneratedFiles();
		if (!empty($generatedFiles)) {
			$output .= "<h3>Generated Files:</h3><div class=\"accordion\">\n";
			$generatedFilesFormatted = $helper->categorizeArray($generatedFiles, 'run_id');
			foreach ($generatedFilesFormatted as $runId => $files) {
				$output .= "<h4 onclick=\"hideMe($(this).next())\">files from run {$runId}</h4><div><table>\n";
				foreach ($files as $file) {
					// TODO status not set if coming from generated files
					$output .= $this->renderFileMenu($rowHtmlId, $file['name'], 'generated', $isUploaded = false, $file['run_id']);
					$rowHtmlId++;
				}
				$output .= "</table></div>\n";
			}
			$output .= "</div>";
		}

		return $output;
	}
	private function renderFileMenu($rowHtmlId, $fileName, $fileStatus, $isUploaded = true, $runId = -1) {
		$downloadLink = "download.php?file_name={$fileName}&" . (($isUploaded) ? "uploaded=true" : "run={$runId}");

		$row = "<tr class=\"{$fileStatus}\" id=\"result_file_{$rowHtmlId}\"><td>" . htmlentities($fileName) . " ({$fileStatus})</td>
			<td><a class=\"button\" onclick=\"previewFile('{$downloadLink}&as_text=true')\">Preview</a></td>
			<td><a class=\"button\" onclick=\"window.location='{$downloadLink}'\">Download</a></td>
			<td><a class=\"button\" onclick=\"$(this).parents('tr').next().toggle('highlight', {}, 500)\">More...</a></td></tr>";

		$targetHtmlRow = "result_file_" . (($rowHtmlId == 0) ? 0 : $rowHtmlId - 1);
		$fileTypeInput = ($isUploaded) ? "<input type=\"hidden\" name=\"uploaded\" value=\"true\">" : "<input type=\"hidden\" name=\"run\" value=\"{$runId}\">";
		$genericForm = "<td><form action=\"#{$targetHtmlRow}\" method=\"POST\" %s>{$fileTypeInput}%s<button type=\"submit\" name=\"%s\" value=\"{$fileName}\">%s</button></form></td>";

		$row .= "<tr style=\"display:none\"><td>&nbsp;</td>";
		$row .= $deleteForm = sprintf($genericForm, $jScript = "onsubmit=\"return confirm('Are you sure you want to delete this file? Action cannot be undone');\"",
			$extraInput = "", $action = "delete", $label = "Delete");
		$row .= $unzipForm = sprintf($genericForm, $jScript = "", $extraInput = "", $action = "unzip", $label = "Unzip");
		$row .= "<td></td></tr>\n";

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
			div.form tr:nth-child(4n+1){background-color:#FFFFE0}
			div.form tr:nth-child(4n+2){background-color:#FFFFE0}
			div.form button{padding:.25em;margin:.25em;font-size:.80em}";
	}
	public function renderSpecificScript() {
		return "function previewFile(url){
			var displayDiv = $('#file_preview');
			displayDiv.css('display', 'block');
			displayDiv.load(url);}";
	}
	public function getScriptLibraries() {
		return array();
	}
}
