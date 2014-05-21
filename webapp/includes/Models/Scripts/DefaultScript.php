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

	public function convertInputToCode(array $input) {
		$inputErrors = array();
		foreach ($this->trueFalseParameters as $name => $parameter) {
			if (!isset($input[$name])) {
				$input[$name] = false;
			}
		}
		foreach ($this->parameters['required'] as $name => $requiredParam) {
			if (!isset($input[$name]) || empty($input[$name])) {
				$inputErrors[] = "A required parameter was not found: " . htmlentities($name);
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
				try {
					$script .= $parameter->renderForOperatingSystem() . " ";
				}
				catch (ScriptException $ex) {
					$inputErrors[] = $ex->getMessage();
				}
			}
		}

		if (!empty($inputErrors)) {
			$errorOutput = "There some were problems with the parameters you submitted:<ul>";
			foreach ($inputErrors as $error) {
				$errorOutput .= "<li>{$error}</li>";
			}
			$errorOutput .= "</ul>\n";
			throw new ScriptException($errorOutput);
		}
		return $script;
	}

	public abstract function getScriptName();
	public abstract function getScriptTitle();
	public abstract function getHtmlId();
	public abstract function renderHelp();
}
