<?php

namespace Models\Scripts;

abstract class DefaultScript implements ScriptI {

	protected $project;
	protected $parameters;

	public function __construct(\Models\Project $project) {
		$this->project = $project;
		$this->parameters = $this->getInitialParameters();
	}
	public function getParameters() {
		return $this->parameters;
	}
	public function renderAsForm() {
		$form = "<h4>{$this->getScriptTitle()} 
			(<a onclick=\"displayHelp('script_help_{$this->getScriptShortTitle()}');\">help</a>)</h4>\n";
		foreach ($this->parameters as $parameter) {
			$form .= $parameter->renderForForm() . "\n";
		}
		return $form;
	}
	public function run() {
		$arbitraryScript = $this->getScriptName() . " ";
		foreach ($this->parameters as $parameter) {
			$arbitraryScript .= $parameter->renderForOperatingSystem() . " ";
		}
		$this->project->getOperatingSystem()->executeArbitraryScript($arbitraryScript);
		return $arbitraryScript;
	}
	public function processInput(array $input) {
		foreach ($input as $inputName => $inputValue) {
			$parameter = $this->parameters[$inputName];
			$parameter->setValue($inputValue);
		}
		return "Once this is working, it will run the script:<br/>{$this->run()}<br/>";
	}

	public abstract function getScriptName();
	public abstract function getScriptTitle();
	public abstract function getScriptShortTitle();
	public abstract function renderHelp();
}
