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

class PickOtus extends DefaultScript {

	public function initializeParameters() {
		$inputSeqsFilePath = new OldFileParameter("--input_seqs_filepath", $this->project);
		$inputSeqsFilePath->requireIf();

		$this->parameterRelationships->makeOptional(array(
			"1" => new Label("<p><strong>Required Parameters</strong></p>"),
			$inputSeqsFilePath->getName() => $inputSeqsFilePath,
			"2" => new Label("<p><strong>Optional Parameters</strong></p>"),
			"--verbose" => new TrueFalseParameter("--verbose"),
			"--otu_picking_method" => new ChoiceParameter("--otu_picking_method", "uclust", 
				array("mothur", "trie", "uclust_ref", "usearch", "usearch_ref", "blast", "usearch61",
					"usearch61_ref", "prefix_suffix", "cdhit", "uclust")),
                        /* TODO The mothur method requires an input file of
                        aligned sequences.*/ 
			"--clustering_algorithm" => new ChoiceParameter("--clustering_algorithm", "furthest",
				array("furthest", "nearest", "average")),
  			"--max_cdhit_memory" => new TextArgumentParameter("--max_cdhit_memory", "400", "/\\d+/"),
			"--output_dir" => new NewFileParameter("--output_dir", "uclust_picked_otus"), // TODO dynamic default
  			"--refseqs_fp" => new OldFileParameter("--refseqs_fp", $this->project), 
			"--blast_db" => new OldFileParameter("--blast_db", $this->project), 
  			"--min_aligned_percent" => new TextArgumentParameter("--min_aligned_percent", "0.5", "/.*/"),
			"--similarity" => new TextArgumentParameter("--similarity", "0.97", "/.*/"),
			"--max_e_value" => new TextArgumentParameter("--max_e_value", "1e-10", "/.*/"),
			"--trie_reverse_seqs" => new TrueFalseParameter("--trie_reverse_seqs"),
			"--prefix_prefilter_length" => new TextArgumentParameter("--prefix_prefilter_length", "", "/\\d+/"),
			"--trie_prefilter" => new TrueFalseParameter("--trie_prefilter"),
  			"--prefix_length" => new TextArgumentParameter("--prefix_length", "50", "/\\d+/"),
  			"--suffix_length" => new TextArgumentParameter("--suffix_length", "50", "/\\d+/"),
			"--enable_rev_strand_match" => new TrueFalseParameter("--enable_rev_strand_match"),
  			"--suppress_presort_by_abundance_uclust" => new TrueFalseParameter("--suppress_presort_by_abundance_uclust"),
  			"--optimal_uclust" => new TrueFalseParameter("--optimal_uclust"),
			"--exact_uclust" => new TrueFalseParameter("--exact_uclust"),
  			"--user_sort" => new TrueFalseParameter("--user_sort"),
			"--suppress_new_clusters" => new TrueFalseParameter("--suppress_new_clusters"),
			"--max_accepts" => new TextArgumentParameter("--max_accepts", "20", "/\\d+/"), // TODO dynamic default
			"--max_rejects" => new TextArgumentParameter("--max_rejects", "500", "/\\d+/"), // TODO dynamic default
			"--stepwords" => new TextArgumentParameter("--stepwords", "20", "/\\d+/"),
			"--word_length" => new TextArgumentParameter("--word_length", "12", "/\\d+/"), // TODO dynamic default
			"--uclust_otu_id_prefix" => new TextArgumentParameter("--uclust_otu_id_prefix", "denovo", "/.*/"), // TODO no whitespace
			"--suppress_uclust_stable_sort" => new TrueFalseParameter("--suppress_uclust_stable_sort"),
  			"--suppress_uclust_prefilter_exact_match" => new TrueFalseParameter("--suppress_uclust_prefilter_exact_match"),
  			"--save_uc_files" => new TrueFalseInvertedParameter("--save_uc_files"),
			"--percent_id_err" => new TextArgumentParameter("--percent_id_err", "0.97", "/.*/"), // TODO decimal between 1 and 0
			"--minsize" => new TextArgumentParameter("--minsize", "4", "/\\d+/"),
			"--abundance_skew" => new TextArgumentParameter("--abundance_skew", "2.0", "/.*/"), // TODO any float
			"--db_filepath" => new OldFileParameter("--db_filepath", $this->project),
			"--perc_id_blast" => new TextArgumentParameter("--perc_id_blast", "0.97", "/.*/"), // TODO fload between 1 and 0
			//"--de_novo_chimera_detection" => new TrueFalseInvertedParameter("--de_novo_chimera_detection"), // TODO deprecated
			"--suppress_de_novo_chimera_detection" => new TrueFalseParameter("--suppress_de_novo_chimera_detection"),
			//"--reference_chimera_detection" => new TrueFalseInvertedParameter("--reference_chimera_detection"), // TODO deprecated
			"--suppress_reference_chimera_detection" => new TrueFalseParameter("--suppress_reference_chimera_detection"),
			//"--cluster_size_filtering" => new TrueFalseInvertedParameter("--cluster_size_filtering"), // TODO deprecated
			"--suppress_cluster_size_filtering" => new TrueFalseParameter("--suppress_cluster_size_filtering"),
			"--remove_usearch_logs" => new TrueFalseParameter("--remove_usearch_logs"),
			"--derep_fullseq" => new TrueFalseParameter("--derep_fullseq"),
			"--non_chimeras_retention" => new ChoiceParameter("--non_chimeras_retention", "union", array("union", "intersect")),
			"--minlen" => new TextArgumentParameter("--minlen", "64", "/\\d+/"),
			"--usearch_fast_cluster" => new TrueFalseParameter("--usearch_fast_cluster"), 
				// TODO can't be used with --enable_rev_strand_matchign
				// TODO forces usearch61_sort_method = long
			"--usearch61_sort_method" => new ChoiceParameter("--usearch61_sort_method", "abundance", 
				array("abundance", "length", "None")),
			"--sizeorder" => new TrueFalseParameter("--sizeorder"),
				//TODO Requires that --usearch61_sort_method be abundance. [default: False]
//			"--threads" => new TextArgumentParameter("--threads", "1.0", "/.*/"),  TODO not supported in all MacQIIME versions 
				// TODO this one is tough...
		));
	}
	public function getScriptName() {
		return "pick_otus.py";
	}
	public function getScriptTitle() {
		return "Pick OTUs";
	}
	public function getHtmlId() {
		return "pick_otus";
	}
	public function renderHelp() {
		return "<p>{$this->getScriptTitle()}</p><p>OTU stands for Operational Taxonomic Unit</p>";
	}

}
