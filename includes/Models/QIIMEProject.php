<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

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
	public function getInitialScripts() {
		$scripts = array();

		$script = new \Models\Scripts\QIIME\ValidateMappingFile($this);
		$scripts[$script->getHtmlId()] = $script;

		$script = new \Models\Scripts\QIIME\JoinPairedEnds($this);
		$scripts[$script->getHtmlId()] = $script;
		$script = new \Models\Scripts\QIIME\SplitLibraries($this);
		$scripts[$script->getHtmlId()] = $script;
		$script = new \Models\Scripts\QIIME\ExtractBarcodes($this);
		$scripts[$script->getHtmlId()] = $script;
		$script = new \Models\Scripts\QIIME\SplitLibrariesFastq($this);
		$scripts[$script->getHtmlId()] = $script;
		$script = new \Models\Scripts\QIIME\ConvertFastaQualFastq($this);
		$scripts[$script->getHtmlId()] = $script;

		$script = new \Models\Scripts\QIIME\PickOtus($this);
		$scripts[$script->getHtmlId()] = $script;
		$script = new \Models\Scripts\QIIME\PickRepSet($this);
		$scripts[$script->getHtmlId()] = $script;

		$script = new \Models\Scripts\QIIME\AssignTaxonomy($this);
		$scripts[$script->getHtmlId()] = $script;
		$script = new \Models\Scripts\QIIME\MakeOtuTable($this);
		$scripts[$script->getHtmlId()] = $script;
		$script = new \Models\Scripts\QIIME\ManipulateOtuTable($this);
		$scripts[$script->getHtmlId()] = $script;

		$script = new \Models\Scripts\QIIME\AlignSeqs($this);
		$scripts[$script->getHtmlId()] = $script;
		$script = new \Models\Scripts\QIIME\FilterAlignment($this);
		$scripts[$script->getHtmlId()] = $script;
		$script = new \Models\Scripts\QIIME\MakePhylogeny($this);
		$scripts[$script->getHtmlId()] = $script;

		return $scripts;
	}

	public function getFormattedScripts() {
		if (!$this->scriptsFormatted) {
			$scriptsInOrder = array_values($this->getScripts());
			$index = 0;
			$this->scriptsFormatted['Validate input'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Prepare libraries'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Prepare libraries'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Prepare libraries'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Prepare libraries'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Prepare libraries'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Organize into OTUs'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Organize into OTUs'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Count/analyze OTUs'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Count/analyze OTUs'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Count/analyze OTUs'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Perform phylogeny analysis'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Perform phylogeny analysis'][] = $scriptsInOrder[$index++];
			$this->scriptsFormatted['Perform phylogeny analysis'][] = $scriptsInOrder[$index];
		}
		return $this->scriptsFormatted;
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
