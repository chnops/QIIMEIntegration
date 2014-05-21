<?php

namespace Models;

class QIIMEProject extends Project {

	public function beginProject() {
		$newId = $this->database->createProject($this->owner, $this->name);
		$this->setId($newId);

		$this->operatingSystem->createDir($this->database->getUserRoot($this->owner) . "/" . $newId);
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
		$scriptId = $allInput['script'];
		unset($allInput['script']);
		if (!isset($this->scripts[$scriptId])) {
			throw new \Exception("Unable to find script: " . htmlentities($scriptId));
		}

		$script = $this->scripts[$scriptId];
		$code = $script->convertInputToCode($allInput);
		$codeOutput = $this->operatingSystem->executeArbitraryScript($this->workflow->getEnvironmentSource(), $this->database->getUserRoot($this->owner) . "/" . $this->getId(), $code);
		$return = "Your script ran successfully!";
		if ($codeOutput) {
			$return .= "<br/>Here is the output from the console: " . htmlentities($codeOutput);
		}

		$savingSucceeded = $this->database->saveRun($this->owner, $this->id, $scriptId, $code);
		if (!$savingSucceeded) {
			$return .= "<br/>Unfortunately, we were unable to save the run in the database.";
			throw new \Exception($return);
		}

		return $return;
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
