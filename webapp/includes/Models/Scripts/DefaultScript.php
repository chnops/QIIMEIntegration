<?php

namespace Models\Scripts;

abstract class DefaultScript implements ScriptI {

	protected $database;
	protected $operatingSystem;

	protected $parameters;

	public function __construct(\Database\DatabaseI $database, \Models\OperatingSystemI $operatingSystem) {
		$this->database = $database;
		$this->operatingSystem = $operatingSystem;
		$this->parameters = $this->getInitialParameters();
	}
	public function getParameters() {
		return $this->parameters;
	}
	public function renderAsForm() {
		$content = "<h4>{$this->getScriptTitle()} 
			(<a onclick=\"displayHelp('script_help_{$this->getScriptShortTitle()}');\">help</a>)</h4>\n";
		foreach ($this->parameters as $parameter) {
			$content .= $parameter->renderForForm() . "\n";
		}
		return $content;
	}
	public function run() {
		$arbitraryScript = $this->getScriptName() . " ";
		foreach ($this->parameters as $parameter) {
			$arbitraryScript .= $parameter->renderForOperatingSystem() . " ";
		}
		$this->operatingSystem->executeArbitraryScript($arbitraryScript);
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
