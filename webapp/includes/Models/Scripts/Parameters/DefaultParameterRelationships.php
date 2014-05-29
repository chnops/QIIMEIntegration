<?php

namespace Models\Scripts\Parameters;

class DefaultParameterRelationships implements ParameterRelationshipsI {
	// TODO allow parameter or array
	
	private $allParameters = array();

	// TODO delete
	private $triggers = array();
	private $usuallyExcluded = array();
	private $conditionalAllowers = array();
	private $alwaysRequired = array();
	private $usuallyOptional = array();
	private $conditionalRequirers = array();
	private $links = array();

	public function makeOptional(array $parameters) {
		$this->allParameters = array_merge($this->allParameters, $parameters);
		return $this->allParameters;
	}

	public function getSortedParameters() {
		$sortedParameters = array();
		// follow up with the rest of the optional ones
		foreach ($this->allParameters as $name => $parameter) {
			$sortedParameters[$name] = $parameter;
		}
		$this->allParameters = $sortedParameters;
		return $this->allParameters;
	}

	public function renderFormCode(\Models\Scripts\ScriptI $script) {
		$htmlId = $script->getHtmlId();
		$formVariable = $htmlId . "_form";
		$formCode = "<script type=\"text/javascript\">\nvar {$formVariable} = $('div#form_{$htmlId} form');\n";

		foreach ($this->alwaysRequired as $requiredParamName) {
			$formCode .= "{$formVariable}.find(\"label[for='{$requiredParamName}']\").css('color', '#cc0000').css('font-weight', 'bold');";
		}
		$formCode .= "\n";
		foreach ($this->usuallyOptional as $requiredParamName) {
			$formCode .= "{$formVariable}.find(\"div[for='{$requiredParamName}']\").css('color', '#cc0000').css('font-weight', 'bold').css('display', 'none');";
		}
		$formCode .= "\n";

		foreach ($this->triggers as $trigger) {
			$triggerVariable = preg_replace("/--/", "", $trigger);
			$formCode .= "var {$triggerVariable} = {$formVariable}.find(\"[name='{$trigger}']\");";

			if (isset($this->conditionalRequirers[$trigger])) {
				$conditionalParams = $this->conditionalRequirers[$trigger];
				foreach ($conditionalParams as $value => $paramNames) {
					$quotedArray = array();
					foreach ($paramNames as $name) {
						$quotedArray[] = "{$formVariable}.find(\"div[for='{$name}']\")";
					}
					$formCode .= "{$triggerVariable}['{$value}_requires'] = [" . implode(",", $quotedArray) . "];";
				}
				$formCode .= "{$triggerVariable}.change(function() {
					{$formVariable}.find('div[for]').css('display', 'none');
					var requiredLabels = {$triggerVariable}[{$triggerVariable}.val() + '_requires'];
					if (requiredLabels) {
						jQuery.each(requiredLabels, function(index, value) {value.css('display', 'block')});
					}
					});";
			}
			if (isset($this->conditionalAllowers[$trigger])) {
				$allConditionalParams = array();
				foreach ($this->usuallyExcluded as $excludedParam) {
					$allConditionalParams[] = "'{$excludedParam}'";
				}
				$formCode .= "{$triggerVariable}['usually_excluded'] = [" . implode(",", $allConditionalParams) . "];";

				$conditionalParamsByValue = $this->conditionalAllowers[$trigger];
				foreach ($conditionalParamsByValue as $value => $paramNames) {
					$quotedArray = array();
					foreach ($paramNames as $name) {
						$quotedArray[] = "'{$name}'";
					}
					$formCode .= "{$triggerVariable}['{$value}_allows'] = [" . implode(",", $quotedArray) . "];";
				}
				$formCode .= "{$triggerVariable}.change(function() {
					jQuery.each({$triggerVariable}['usually_excluded'], function(index, value) {
						{$formVariable}.find('[name=\"' + value + '\"]').prop('disabled', true).parents('label').css('display', 'none');
					});
					var allowedParameters = {$triggerVariable}[{$triggerVariable}.val() + '_allows'];
					if (allowedParameters) {
						jQuery.each(allowedParameters, function(index, value) {
							{$formVariable}.find('[name=\"' + value + '\"]').prop('disabled', false).parents('label').css('display', 'block')
						});
					}
					});";
			}
			$formCode .= "{$triggerVariable}.change();";
		}
		$formCode .= "\n";

		foreach ($this->links as $link) {
			$linkVariable = preg_replace("/\-/", "", $link[0]->getName());
			$defaultValue = ($link[0]->getValue()) ? $link[0]->getValue() : $link[1];
			$formCode .= "var {$linkVariable} = {$formVariable}.find(\"input[name='{$link[0]->getName()}']\");\n";
			$formCode .= "{$linkVariable}.each(function(index, value) {
				$(this).eq(index-1).change(function() {
				$(this).parents(\"table\").find(\"td:nth-child(2) [name]\").prop('disabled', true);
				$(this).parents(\"tr\").find(\"td:nth-child(2) [name]\").prop('disabled', false);
			});});
				{$formVariable}.find(\"input[value='{$defaultValue}']\").click();
				";
		}

		$formCode .= "</script>";
		return $formCode;
	}
}
