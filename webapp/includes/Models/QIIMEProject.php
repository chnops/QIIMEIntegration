<?php

namespace Models;

class QIIMEProject extends Project {

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

		$script = new \Models\Scripts\QIIME\SplitLibraries($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['De-multiplex libraries'][] = $script;

		$script = new \Models\Scripts\QIIME\PickOtus($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Organize into OTUs'][] = $script;

		$script = new \Models\Scripts\QIIME\PickRepSet($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Organize into OTUs'][] = $script;

		$script = new \Models\Scripts\QIIME\MakeOtuTable($this);
		$this->scripts[$script->getHtmlId()] = $script;
		$this->scriptsFormatted['Count/analyze OTUs'][] = $script;

		$script = new \Models\Scripts\QIIME\AssignTaxonomy($this);
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
		);
	}
	public function runScript(array $allInput) {
		$this->generatedFiles = array();
		$this->pastScriptRuns = array();

		$scripts = $this->getScripts();
		$scriptId = $allInput['script'];
		unset($allInput['script']);
		if (!isset($scripts[$scriptId])) {
			throw new \Exception("Unable to find script: " . htmlentities($scriptId));
		}
		$script = $scripts[$scriptId];

		$code = $script->convertInputToCode($allInput);
		$result = "Script input is valid-";

		$runId = $this->database->saveRun($this->owner, $this->id, $scriptId, $code);
		if (!$runId) {
			$result .= "<br/>However, we were unable to save the run in the database.";
			throw new \Exception($result);
		}

		$version = "";
		$consoleError = false;

		$projectDir = $this->getProjectDir();
		$runDir = $projectDir . "/r" . $runId;
		try {
			$this->operatingSystem->createDir($runDir);

			$version = $this->operatingSystem->executeArbitraryScript($this->workflow->getEnvironmentSource(), $runDir, $script->getScriptName() . " --version");
			$version = trim($version);

			$codeOutput = $this->operatingSystem->executeArbitraryScript($this->workflow->getEnvironmentSource(), $runDir, $code);

			$result .= "<br/>Script run successful!";
			$codeOutput = trim($codeOutput);
			if ($codeOutput) {
				$result .= "<br/>Here is the output from the console:<br/>" . htmlentities($codeOutput);
			}

		}
		catch (OperatingSystemException $ex) {
			$consoleError = true;
			$codeOutput = $ex->getConsoleOutput();
			$result .= "<br/>There was a problem with the operating system: " . htmlentities(trim($ex->getMessage()));
		}

		$outputSaveResult = $this->database->addRunResults($runId, $codeOutput, $version);
		if (!$outputSaveResult) {
			$conjunction = ($consoleError) ? "Also" : "However";
			$result .= "<br/><br/>{$conjunction}, we were unable to save the results to the database.";
		}

		if ($consoleError) {
			throw new \Exception($result);
		}
		return $result;
	}

	public function renderOverview() {
		$overview = "<style>div#project_overview{border:2px #999966 ridge;padding:.5em .5em 1.5em .5em;overflow:auto}div#project_overview td{padding:.5em .25em;white-space:nowrap}div#project_overview a.button{min-width:100%}</style>\n";
		$overview .= "<div id=\"project_overview\">\n<table>\n";

		foreach ($this->scriptsFormatted as $category => $scriptArray) {
			$overview .= "<tr><td>{$category}</td>";
			foreach ($scriptArray as $script) {
				$overview .= "<td><a class=\"button\" onclick=\"displayHideables('{$script->getHtmlId()}');\">{$script->getScriptTitle()}</a></td>";
			}
			$overview .= "</tr>\n";
		}
		$overview .= "</table>\n</div>\n";
		return $overview;
	}

}
