<?php

namespace Models;

class QIIMEProject extends Project {

	public function beginProject() {
		$newId = $this->database->createProject($this->owner, $this->name);
		$this->setId($newId);

		$this->operatingSystem->createDir($this->database->getUserRoot($this->owner) . "/" . $newId);
	}
	public function getInitialScripts() {
		return array(
			new \Models\Scripts\QIIME\ValidateMappingFile($this),
			new \Models\Scripts\QIIME\SplitLibraries($this),
		);
	}
	public function getInitialFileTypes() {
		return array(
			new MapFileType(),
			new SequenceFileType(),
			new SequenceQualityFileType(),
		);
	}
	public function processInput(array $allInput) {
		ob_start();

		$this->setName($allInput['project_name']);
		unset($allInput['project_name']);
		$this->setOwner($allInput['project_owner']);
		unset($allInput['project_owner']);
		// TODO process file {$_FILES['project_input_file']}
		unset($_FILES['project_input_file']);

		foreach ($this->scripts as $script) {
			echo $script->processInput($allInput);
		}

		return ob_get_clean();
	}

	public function renderOverview() {
		ob_start();
?>
<style>
div#project_overview{border:2px #999966 ridge;padding:.5em}
div#project_overview td{padding:.5em .25em}
div#project_overview a.button{min-width:100%}
</style>
<div id="project_overview">
<table>
<tr><td>Validate input</td><td><a class="button" onclick="displayHideables('validate_mapping_file');">validate_mapping_file.py</a></td><td><a class="button">identify_chimeric_seqs.py</a></td><td><a class="button">exclude_seqs_by_blast.py</a></td></tr>
<tr><td>De-multiplex libraries</td><td><a class="button" onclick="displayHideables('split_libraries');">split_libraries.py</a></td></tr>
<tr><td>Organize into OTUs</td><td><a class="button">pick_otus.py</a></td><td><a class="button">pick_rep_sets.py</a></td></tr>
<tr><td>Count/analyze OTUs</td><td><a class="button">make_otu_table.py</a></td><td><a class="button">assign_taxonomy.py</a></td></tr>
<tr><td>Perform phylogeny analysis</td><td><a class="button">align_seqs.py</a></td><td><a class="button">filter_alignment.py</a></td><td><a class="button">make_phylogeny.py</a></td></tr>
</table>			
</div>
<?php
		return ob_get_clean();
	}

}
