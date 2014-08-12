<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\QIIME;
use Models\Scripts\DefaultScript;
use Models\Scripts\Parameters\VersionParameter;
use Models\Scripts\Parameters\HelpParameter;
use Models\Scripts\Parameters\TextArgumentParameter;
use Models\Scripts\Parameters\TrueFalseParameter;
use Models\Scripts\Parameters\TrueFalseInvertedParameter;
use Models\Scripts\Parameters\NewFileParameter;
use Models\Scripts\Parameters\OldFileParameter;
use Models\Scripts\Parameters\ChoiceParameter;
use Models\Scripts\Parameters\Label;

class  extends DefaultScript {
	public function getScriptName() {
		return "dummy_script.py";
	}
	public function getScriptTitle() {
		return "Implement me!";
	}
	public function getHtmlId() {
		return "";
	}

	public function getInitialParameters() {
		$parameters = parent::getInitialParameters();

		array_push($parameters,
			new Label("Required Parameters"),

			new Label("Optional parameters"),

			new Label("Output options"),
		);
		return $parameters;
	}
}
