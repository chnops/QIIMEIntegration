<?php

namespace Models\Scripts;
use Models\Scripts\Parameters\HelpParameter;
use Models\Scripts\Parameters\VersionParameter;

abstract class DefaultScript implements ScriptI, \Models\HideableI {

	protected $project = NULL;
	protected $parameters = array();

	public function __construct(\Models\ProjectI $project) {
		$this->project = $project;
	}
	public function initializeParameters() {
		$helpParameter = new HelpParameter($this);
		$versionParameter = new VersionParameter($this);
		$helpParameter->excludeButAllowIf();
		$versionParameter->excludeButAllowIf();
		$this->parameters = array($helpParameter->getName() => $helpParameter,
			$versionParameter->getName() => $versionParameter,);
	}
	public function getParameters() {
		if (empty($this->parameters)) {
			$this->initializeParameters();
		}
		return $this->parameters;
	}
	public function renderAsForm($disabled) {
		$disabledString = ($disabled) ? " disabled" : "";
		$form = "<form method=\"POST\"><h4>{$this->getScriptTitle()} - {$this->getScriptName()}</h4>\n";
		foreach ($this->getParameters() as $parameter) {
			$form .= $parameter->renderForForm($disabled) . "\n";
		}

		$form .= "<input type=\"hidden\" name=\"step\" value=\"run\"{$disabledString}/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->getHtmlId()}\"{$disabledString}/>
			<button type=\"submit\"{$disabledString}>Run</button>\n</form>";

		if (!$disabled) {
			$formJsVar = "js_" . $this->getHtmlId();
			$form .= "<script type=\"text/javascript\">\nvar {$formJsVar} = $('div#form_{$this->getHtmlId()} form');\n";
			foreach ($this->getParameters() as $parameter) {
				$form .= $parameter->renderFormScript($formJsVar, $disabled);
			}
			$form .= "</script>\n";
		}
		return $form;
	}

	public function acceptInput(array $input) {
		$inputErrors = array();
		foreach ($this->getParameters() as $parameter) {
			try {
				$parameter->acceptInput($input);
			}
			catch (ScriptException $ex) {
				$inputErrors[] = $ex->getMessage();
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

	}

	public function renderCommand() {
		$script = $this->getScriptName() . " ";
		foreach ($this->getParameters() as $parameter) {
			$script .= $parameter->renderForOperatingSystem() . " ";
		}
		return $script;
	}
	public function renderHelp() {
		ob_start();
		echo "<p><strong>{$this->getScriptTitle()} - {$this->getScriptName()}</strong></p>";
		include "views/{$this->getHtmlId()}.html";
		return ob_get_clean();
	}

	public abstract function getScriptName();
	public abstract function getScriptTitle();
	public abstract function getHtmlId();
}
