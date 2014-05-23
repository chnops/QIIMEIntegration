<?php

namespace Models\Scripts;

abstract class DefaultScript implements ScriptI, \Models\HideableI {

	protected $project = null;
	protected $parameters = array();
	protected $trueFalseParameters = array();
	protected $parameterDependencyRelationships = array();
	protected $parameterRequirementRelationships = array();

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
		$form .= $this->getScriptForDependentParameters();
		$form .= $this->getScriptForConditionallyRequiredParameters();
		return $form;
	}

	public function acceptInput(array $input) {
		$inputErrors = array();
		// prep parameters TODO improve design
		foreach ($this->trueFalseParameters as $name => $parameter) {
			if (!isset($input[$name])) {
				$input[$name] = false;
			}
		}

		// check required parameters
		foreach ($this->parameters['required'] as $name => $requiredParam) {
			if (!isset($input[$name]) || empty($input[$name])) {
				$inputErrors[] = "A required parameter was not found: " . htmlentities($name);
			}
			else {
				$requiredParam->setValue($input[$name]);
			}
		}

		// setValue (may throw exception)
		foreach ($input as $inputName => $inputValue) {
			$parameter = $this->parameters['special'][$inputName];
			try {
				$parameter->setValue($inputValue);
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
		foreach ($this->parameters as $category => $parameterArray) {
			foreach ($parameterArray as $parameter) {
				$script .= $parameter->renderForOperatingSystem() . " ";
			}
		}
		return $script;
	}

	public function getScriptForDependentParameters() {
		$script = "<script type=\"text/javascript\">\nvar thisForm = $('div#form_{$this->getHtmlId()} form');\n";
		foreach ($this->parameterDependencyRelationships as $dependencyTrigger => $dependentParameters) {
			$script .= "var trigger = thisForm.find('select[name=\"{$dependencyTrigger}\"]');\n";
			$function = "trigger.change(function(Event) {var thisForm = $('div#form_{$this->getHtmlId()} form');\n";

			$disablePart = "var allInputs = [";
			$enablePart = "var trigger = Event.target;var selectedOptObj = trigger[trigger.selectedIndex];var value = selectedOptObj.textContent;\n";
			$enablePart .= "switch(value) {\n";
			foreach ($dependentParameters as $triggerValue => $parameterNames) {
				$enablePart .= "case \"{$triggerValue}\":\n";
				foreach ($parameterNames as $name) {
					$disablePart .= "thisForm.find(\"[name='{$name}']\"),";
					$enablePart .= "enable(thisForm.find(\"[name='{$name}']\"));\n";
				}
				$enablePart .= "break;\n";
			}
			$disablePart .= "];jQuery.each(allInputs, function(index, Element) { disable(Element); } );\n";
			$enablePart .= "}";

			$function .= $disablePart . $enablePart . "});\n";
			$script .= $function . "trigger.trigger('change');";
		}
		$script .= "</script>\n";
		return $script;
	}
	public function getScriptForConditionallyRequiredParameters() {
		$script = "<script type=\"text/javascript\">\nvar thisForm = $('div#form_{$this->getHtmlId()} form');\n";
		foreach ($this->parameterRequirementRelationships as $requirementTrigger => $requiredParameters) {
			$script .= "var trigger = thisForm.find('select[name=\"{$requirementTrigger}\"]');\n";
			$function = "trigger.change(function(Event) {var thisForm = $('div#form_{$this->getHtmlId()} form');\n";

			$disablePart = "var allInputs = [";
			$enablePart = "var trigger = Event.target;var selectedOptObj = trigger[trigger.selectedIndex];var value = selectedOptObj.textContent;\n";
			$enablePart .= "switch(value) {\n";
			foreach ($requiredParameters as $triggerValue => $parameterNames) {
				$enablePart .= "case \"{$triggerValue}\":\n";
				foreach ($parameterNames as $name) {
					$disablePart .= "thisForm.find(\"#{$this->getHtmlId()}_{$name}\"),";
					$enablePart .= "console.log(thisForm.find(\"#{$this->getHtmlId()}_{$name}\").css('display', 'inline').attr('name'));\n";
				}
				$enablePart .= "break;\n";
			}
			$disablePart .= "];jQuery.each(allInputs, function(index, Element) { Element.css('display', 'none') } );\n";
			$enablePart .= "}";

			$function .= $disablePart . $enablePart . "});\n";
			$script .= $function . "trigger.trigger('change');";
		}	
		$script .= "</script>";
		return $script;
	}
	public abstract function getScriptName();
	public abstract function getScriptTitle();
	public abstract function getHtmlId();
	public abstract function renderHelp();
	public abstract function initializeParameters();
}
