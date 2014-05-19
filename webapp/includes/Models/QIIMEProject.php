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
div#project_overview{background-color:white}
div#project_overview td{border-style:outset;}
div#project_overview tr{border-bottom:2px;margin-bottom:1em;}
</style>
<div id="project_overview">
<table>
<tr><td class="category">Validate input</td><td>validate_mapping_file.py</td><td>identify_chimeric_seqs.py</td><td>exclude_seqs_by_blast.py</td></tr>
<tr><td class="category">De-multiplex libraries</td><td>split_libraries.py</td></tr>
<tr><td class="category">Organize into OTUs</td><td>pick_otus.py</td><td>pick_rep_sets.py</td></tr>
<tr><td class="category">Count/analyze OTUs</td><td>make_otu_table.py</td><td>assign_taxonomy.py</td></tr>
<tr><td class="category">Perform phylogeny analysis</td><td>align_seqs.py</td><td>filter_alignment.py</td><td>make_phylogeny.py</td></tr>
</table>			
</div>
<?php
		return ob_get_clean();
	}

}
