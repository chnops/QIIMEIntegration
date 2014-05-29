<?php

namespace Models\Scripts;
use Models\Scripts\Parameters\DefaultParameterRelationships;
use Models\Scripts\Parameters\HelpParameter;
use Models\Scripts\Parameters\VersionParameter;

abstract class DefaultScript implements ScriptI, \Models\HideableI {

	protected $project = null;
	protected $parameterRelationships;

	public function __construct(\Models\Project $project) {
		$this->project = $project;
		$this->parameterRelationships = new DefaultParameterRelationships();

		$helpParameter = new HelpParameter($this);
		$versionParameter = new VersionParameter($this->project, $this->getScriptName());
		$helpParameter->excludeButAllowIf();
		$versionParameter->excludeButAllowIf();
		$this->parameterRelationships->makeOptional(
			array(
				$helpParameter->getName() => $helpParameter,
				$versionParameter->getName() => $versionParameter,
			)
		);

		$this->initializeParameters();
	}
	public function getParameters() {
		return $this->parameterRelationships->getSortedParameters();
	}
	public function renderAsForm($disabled) {
		$disabledString = ($disabled) ? " disabled" : "";
		$form = "<form method=\"POST\"><h4>{$this->getScriptTitle()}</h4>\n";
		foreach ($this->getParameters() as $parameter) {
			$form .= $parameter->renderForForm($disabled) . "\n";
		}
		$form .= "<input type=\"hidden\" name=\"step\" value=\"run\"{$disabledString}/>
			<input type=\"hidden\" name=\"script\" value=\"{$this->getHtmlId()}\"{$disabledString}/>
			<button type=\"submit\"{$disabledString}>Run</button>\n";

		if (!$disabled) {
			$form .= $this->parameterRelationships->renderFormCode($this);
		}

		$form .= "</form>\n";
		return $form;
	}

	public function acceptInput(array $input) {
		$inputErrors = array();
		foreach ($this->getParameters() as $name => $parameter) {
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

	public abstract function getScriptName();
	public abstract function getScriptTitle();
	public abstract function getHtmlId();
	public abstract function renderHelp();
	public abstract function initializeParameters();
}
