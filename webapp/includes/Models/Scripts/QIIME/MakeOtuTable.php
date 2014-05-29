<?php

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

class MakeOtuTable extends DefaultScript {

	public function initializeParameters() {
		$otuMapFile = new OldFileParameter("--otu_map_fp", $this->project);
		$otuMapFile->requireIf();
		// TODO dynamic default / no default
		$outputBiomFp = new NewFileParameter("--output_biom_fp", "_.biom");
		$outputBiomFp->requireIf();

		$this->parameterRelationships->makeOptional(array(
			"1" => new Label("<p><strong>Required Parameters</strong></p>"),
			$otuMapFile->getName() => $otuMapFile,
			$outputBiomFp->getName() => $outputBiomFp, 
			"2" => new Label("<p><strong>Optional Parameters</strong></p>"),
			"--verbose" => new TrueFalseParameter("--verbose"),
			"--taxonomy" => new OldFileParameter("--taxonomy", $this->project),
			"--exclude_otus_fp" => new OldFileParameter("--exclude_otus_fp", $this->project),
		));
	}
	public function getScriptName() {
		return "make_otu_table.py";
	}
	public function getScriptTitle() {
		return "Make OTU table";
	}
	public function getHtmlId() {
		return "make_otu_table";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>An OTU table contains organized information about the abundance of different OTUs in a set of sequences.</p>";
	}

}
