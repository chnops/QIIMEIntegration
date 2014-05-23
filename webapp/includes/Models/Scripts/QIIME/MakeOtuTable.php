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

class MakeOtuTable extends DefaultScript {

	public function initializeParameters() {
		$otuMapFile = new OldFileParameter("--otu_map_fp", $this->project);
		$this->parameterRelationships->requireParam($otuMapFile);
		$outputBiomFp = new OldFileParameter("--output_biom_fp", $this->project);
		$this->parameterRelationships->requireParam($outputBiomFp);

		$this->parameterRelationships->makeOptional(array(
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
