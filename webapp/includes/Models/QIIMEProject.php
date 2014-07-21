<?php

namespace Models;

class QIIMEProject extends DefaultProject {

	private $builtInFiles = array();

	public function beginProject() {
		$this->database->startTakingRequests();
		$newId = $this->database->createProject($this->owner, $this->name);
		if (!$newId) {
			$this->database->forgetAllRequests();
			throw new \Exception("There was a problem with the database.");
		}
		$this->setId($newId);

		$projectDir = $this->getProjectDir();
		try {
			$this->operatingSystem->createDir($projectDir);
			$this->operatingSystem->createDir($projectDir . "/uploads/");
			$this->database->executeAllRequests();
		}
		catch (OperatingSystemException $ex) {
			$this->operatingSystem->removeDirIfExists($projectDir);
			$this->database->forgetAllRequests();
			throw $ex;
		}
	}
	public function initializeScripts() {
		$script = new \Models\Scripts\QIIME\ValidateMappingFile($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Validate input'][] = $script;

		$script = new \Models\Scripts\QIIME\JoinPairedEnds($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Prepare libraries'][] = $script;

		$script = new \Models\Scripts\QIIME\SplitLibraries($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Prepare libraries'][] = $script;

		$script = new \Models\Scripts\QIIME\ExtractBarcodes($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Prepare libraries'][] = $script;

		$script = new \Models\Scripts\QIIME\SplitLibrariesFastq($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Prepare libraries'][] = $script;

		$script = new \Models\Scripts\QIIME\ConvertFastaQualFastq($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Prepare libraries'][] = $script;

		$script = new \Models\Scripts\QIIME\PickOtus($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Organize into OTUs'][] = $script;

		$script = new \Models\Scripts\QIIME\PickRepSet($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Organize into OTUs'][] = $script;

		$script = new \Models\Scripts\QIIME\AssignTaxonomy($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Count/analyze OTUs'][] = $script;

		$script = new \Models\Scripts\QIIME\MakeOtuTable($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Count/analyze OTUs'][] = $script;

		$script = new \Models\Scripts\QIIME\ManipulateOtuTable($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Count/analyze OTUs'][] = $script;

		$script = new \Models\Scripts\QIIME\AlignSeqs($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Perform phylogeny analysis'][] = $script;

		$script = new \Models\Scripts\QIIME\FilterAlignment($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Perform phylogeny analysis'][] = $script;

		$script = new \Models\Scripts\QIIME\MakePhylogeny($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Perform phylogeny analysis'][] = $script;
	}

	public function getInitialFileTypes() {
		return array(
			new MapFileType(),
			new SequenceFileType(),
			new SequenceQualityFileType(),
			new FastqFileType(),
		);
	}
	public function runScript(array $allInput) {
		$helper = \Utils\Helper::getHelper();
		$this->generatedFiles = array();
		$this->pastScriptRuns = array();

		$scripts = $this->getScripts();
		$scriptId = $allInput['script'];
		unset($allInput['script']);
		if (!isset($scripts[$scriptId])) {
			throw new \Exception("Unable to find script: " . $helper->htmlentities($scriptId));
		}
		$script = $scripts[$scriptId];

		$script->acceptInput($allInput); // will throw an error if bad input
		$result = "Able to validate script input-";
		$code = $script->renderCommand();

		$this->database->startTakingRequests();
		$runId = $this->database->createRun($this->owner, $this->id, $scriptId, $code);
		if (!$runId) {
			$result .= "<br/>However, we were unable to save the run in the database.";
			throw new \Exception($result);
		}

		try {
			$pid = $this->operatingSystem->runScript($this, $runId, $script, $this->database);

			$pidResult = $this->database->giveRunPid($runId, $pid);
			if (!$pidResult) {
				$result .= "<br/>However, we were unable to save the run in the database.";
				$this->database->forgetAllRequests();
				throw new \Exception($result);
			}

			$this->database->executeAllRequests();
			return $result . "<br/>Script was started successfully";
		}
		catch (\Exception $ex) {
			$this->database->forgetAllRequests();
			throw $ex;
		}
	}

	public function renderOverview() {
		$overview = "<div id=\"project_overview\">\n";

		if (!$this->scriptsFormatted) {
			$this->initializeScripts();
		}
		foreach ($this->scriptsFormatted as $category => $scriptArray) {
			$overview .= "<div><span>{$category}</span>";
			foreach ($scriptArray as $script) {
				$overview .= "<span><a class=\"button\" onclick=\"displayHideables('{$script->getHtmlId()}');\" title=\"{$script->getScriptName()}\">{$script->getScriptTitle()}</a></span>";
			}
			$overview .= "</div>\n";
		}
		$overview .= "</div>\n";
		return $overview;
	}

	public function retrieveAllBuiltInFiles() {
		if (empty($this->builtInFiles)) {
			try {
				$fileNames = $this->operatingSystem->getDirContents('/macqiime/greengenes', $prependHome = false);
				foreach ($fileNames as $fileName) {
					$this->builtInFiles[] = "/macqiime/greengenes/{$fileName}";
				}
				$fileNames = $this->operatingSystem->getDirContents('/macqiime/UNITe', $prependHome = false);
				foreach ($fileNames as $fileName) {
					$this->builtInFiles[] = "/macqiime/UNITe/{$fileName}";
				}
			}
			catch (OperatingSystemException $ex) {
				error_log("Unable to retrieve built in files: " . $ex->getMessage());
				$this->builtInFiles = array();
			}
		}
		return $this->builtInFiles;
	}

	public function getEnvironmentSource() {
		return "/macqiime/configs/bash_profile.txt";
	}

}
