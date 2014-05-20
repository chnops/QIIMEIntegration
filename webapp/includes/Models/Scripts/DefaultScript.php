<?php

namespace Models\Scripts;

abstract class DefaultScript implements ScriptI, \Models\HideableI {

	protected $project;
	protected $parameters;
	protected $trueFalseParameters = array();

	public function __construct(\Models\Project $project) {
		$this->project = $project;
		$this->initializeParameters();
	}
	public function getParameters() {
		return $this->parameters;
	}
	public function renderAsForm() {
		$form = "<form method=\"POST\"><h4>{$this->getScriptTitle()}</h4>\n";
		foreach ($this->parameters as $parameter) {
			$form .= $parameter->renderForForm() . "\n";
		}
		$form .= "<input type=\"hidden\" name=\"step\" value=\"run\"/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->getHtmlId()}\"/>
			<button type=\"submit\">Run</button></form>\n";
		return $form;
	}
	public function run() {
		$arbitraryScript = $this->getScriptName() . " ";
		foreach ($this->parameters as $parameter) {
			$arbitraryScript .= $parameter->renderForOperatingSystem() . " ";
		}
//		$this->project->getOperatingSystem()->executeArbitraryScript($arbitraryScript);
		return $arbitraryScript;
	}
	public function processInput(array $input) {
		foreach ($this->trueFalseParameters as $name => $parameter) {
			if (!isset($input[$name])) {
				$input[$name] = false;
			}
		}
		foreach ($input as $inputName => $inputValue) {
			$parameter = $this->parameters[$inputName];
			$parameter->setValue($inputValue);
		}
		return "Once this is working, it will run the script:<br/>{$this->run()}<br/>";
	}

	public abstract function getScriptName();
	public abstract function getScriptTitle();
	public abstract function getHtmlId();
	public abstract function renderHelp();
}
