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
