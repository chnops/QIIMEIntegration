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
		$content = $this->getScriptTitle() . "\n";
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
	}

	public abstract function getScriptName();
	public abstract function getScriptTitle();
	public abstract function renderHelp();
}
