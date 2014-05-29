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

		array_push($this->parameters,
			 new Label("<p><strong>Required Parameters</strong></p>"),
			 $inputSeqsFilePath,
			 new Label("<p><strong>Optional Parameters</strong></p>"),
			 new TrueFalseParameter("--verbose"),
			 new ChoiceParameter("--otu_picking_method", "uclust", 
				array("mothur", "trie", "uclust_ref", "usearch", "usearch_ref", "blast", "usearch61",
					"usearch61_ref", "prefix_suffix", "cdhit", "uclust")),
                        /* TODO The mothur method requires an input file of
                        aligned sequences.*/ 
			 new ChoiceParameter("--clustering_algorithm", "furthest",
				array("furthest", "nearest", "average")),
  			 new TextArgumentParameter("--max_cdhit_memory", "400", "/\\d+/"),
			 new NewFileParameter("--output_dir", "uclust_picked_otus"), // TODO dynamic default
  			 new OldFileParameter("--refseqs_fp", $this->project), 
			 new OldFileParameter("--blast_db", $this->project), 
  			 new TextArgumentParameter("--min_aligned_percent", "0.5", "/.*/"),
			 new TextArgumentParameter("--similarity", "0.97", "/.*/"),
			 new TextArgumentParameter("--max_e_value", "1e-10", "/.*/"),
			 new TrueFalseParameter("--trie_reverse_seqs"),
			 new TextArgumentParameter("--prefix_prefilter_length", "", "/\\d+/"),
			 new TrueFalseParameter("--trie_prefilter"),
  			 new TextArgumentParameter("--prefix_length", "50", "/\\d+/"),
  			 new TextArgumentParameter("--suffix_length", "50", "/\\d+/"),
			 new TrueFalseParameter("--enable_rev_strand_match"),
  			 new TrueFalseParameter("--suppress_presort_by_abundance_uclust"),
  			 new TrueFalseParameter("--optimal_uclust"),
			 new TrueFalseParameter("--exact_uclust"),
  			 new TrueFalseParameter("--user_sort"),
			 new TrueFalseParameter("--suppress_new_clusters"),
			 new TextArgumentParameter("--max_accepts", "20", "/\\d+/"), // TODO dynamic default
			 new TextArgumentParameter("--max_rejects", "500", "/\\d+/"), // TODO dynamic default
			 new TextArgumentParameter("--stepwords", "20", "/\\d+/"),
			 new TextArgumentParameter("--word_length", "12", "/\\d+/"), // TODO dynamic default
			 new TextArgumentParameter("--uclust_otu_id_prefix", "denovo", "/.*/"), // TODO no whitespace
			 new TrueFalseParameter("--suppress_uclust_stable_sort"),
  			 new TrueFalseParameter("--suppress_uclust_prefilter_exact_match"),
  			 new TrueFalseInvertedParameter("--save_uc_files"),
			 new TextArgumentParameter("--percent_id_err", "0.97", "/.*/"), // TODO decimal between 1 and 0
			 new TextArgumentParameter("--minsize", "4", "/\\d+/"),
			 new TextArgumentParameter("--abundance_skew", "2.0", "/.*/"), // TODO any float
			 new OldFileParameter("--db_filepath", $this->project),
			 new TextArgumentParameter("--perc_id_blast", "0.97", "/.*/"), // TODO fload between 1 and 0
			 //new TrueFalseInvertedParameter("--de_novo_chimera_detection"), // TODO deprecated
			 new TrueFalseParameter("--suppress_de_novo_chimera_detection"),
			 //new TrueFalseInvertedParameter("--reference_chimera_detection"), // TODO deprecated
			 new TrueFalseParameter("--suppress_reference_chimera_detection"),
			 //new TrueFalseInvertedParameter("--cluster_size_filtering"), // TODO deprecated
			 new TrueFalseParameter("--suppress_cluster_size_filtering"),
			 new TrueFalseParameter("--remove_usearch_logs"),
			 new TrueFalseParameter("--derep_fullseq"),
			 new ChoiceParameter("--non_chimeras_retention", "union", array("union", "intersect")),
			 new TextArgumentParameter("--minlen", "64", "/\\d+/"),
			 new TrueFalseParameter("--usearch_fast_cluster"), 
				// TODO can't be used with --enable_rev_strand_matchign
				// TODO forces usearch61_sort_method = long
			 new ChoiceParameter("--usearch61_sort_method", "abundance", 
				array("abundance", "length", "None")),
			 new TrueFalseParameter("--sizeorder")
				//TODO Requires that --usearch61_sort_method be abundance. [default: False]
//			 new TextArgumentParameter("--threads", "1.0", "/.*/"),  TODO not supported in all MacQIIME versions 
				// TODO this one is tough...
		);
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
