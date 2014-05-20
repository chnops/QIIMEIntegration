<?php

namespace Models\Scripts;

abstract class DefaultScript implements ScriptI, \Models\HideableI {

	protected $project;
	protected $parameters;
	protected $trueFalseParameters = array();

	public function __construct(\Models\Project $project) {
		$this->project = $project;
		$this->parameters['common'] = array (
			"--version" => new VersionParameter($this->project, $this->getScriptName()),
			"--help" => new HelpParameter($this),
		);
		$this->initializeParameters();
	}
	public function getParameters() {
		return $this->parameters;
	}
	public function renderAsForm() {
		$form = "<form method=\"POST\"><h4>{$this->getScriptTitle()}</h4>\n";
		foreach ($this->parameters['common'] as $parameter) {
			$form .= $parameter->renderForForm() . "\n";
		}
		$form .= "<p><strong>Required inputs</strong></p>\n";
		foreach ($this->parameters['required'] as $parameter) {
			$form .= $parameter->renderForForm() . "\n";
		}
		$form .= "<hr/><p><strong>Optional inputs</strong></p>\n";
		foreach ($this->parameters['special'] as $parameter) {
			$form .= $parameter->renderForForm() . "\n";
		}
		$form .= "<input type=\"hidden\" name=\"step\" value=\"run\"/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->getHtmlId()}\"/>
			<button type=\"submit\">Run</button></form>\n";
		return $form;
	}

	public function processInput(array $input) {
		foreach ($this->trueFalseParameters as $name => $parameter) {
			if (!isset($input[$name])) {
				$input[$name] = false;
			}
		}
		foreach ($this->parameters['required'] as $name => $requiredParam) {
			if (!isset($input[$name])) {
				throw new \Exception("A required parameter was not found: {$name}");
			}
			else if (empty($input[$name])) {
				throw new \Exception("A required parameter was not found: {$name}");
			}

			$requiredParam->setValue($input[$name]);
			unset($input[$name]);
		}
		foreach ($input as $inputName => $inputValue) {
			$parameter = $this->parameters['special'][$inputName];
			$parameter->setValue($inputValue);
		}

		$script = $this->getScriptName() . " ";
		foreach ($this->parameters as $category => $parameterArray) {
			foreach ($parameterArray as $parameter) {
				$script .= $parameter->renderForOperatingSystem() . " ";
			}
		}
		return $script;
	}

	public abstract function getScriptName();
	public abstract function getScriptTitle();
	public abstract function getHtmlId();
	public abstract function renderHelp();
}
