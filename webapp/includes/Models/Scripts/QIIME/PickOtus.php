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

		$triePrefilter = new TrueFalseParameter("--trie_prefilter");
		$prefixPrefilterLength = new TextArgumentParameter("--prefix_prefilter_length", "", TextArgumentParameter::PATTERN_DIGIT);
		$prefixPrefilterLength->excludeButAllowIf($triePrefilter);

		$otuPickingMethod = new ChoiceParameter("--otu_picking_method", "uclust", 
			array("uclust", "uclust_ref", "blast", "mothur", "cdhit", "usearch", "usearch_ref", "usearch61",
				"usearch61_ref", "prefix_suffix", "trie"));

		$clusteringAlgorithm = new ChoiceParameter("--clustering_algorithm", "furthest",
			array("furthest", "nearest", "average"));
		$clusteringAlgorithm->excludeButAllowIf($otuPickingMethod, "mother");

		$maxCdhitMemory = new TextArgumentParameter("--max_cdhit_memory", "400", TextArgumentParameter::PATTERN_DIGIT); // TODO units = Mbyte
		$maxCdhitMemory->excludeButAllowIf($otuPickingMethod, "cdhit");

		$trieReverseSeqs = new TrueFalseParameter("--trie_reverse_seqs");
		$trieReverseSeqs->excludeButAllowIf($otuPickingMethod, "trie");

		$prefixLength = new TextArgumentParameter("--prefix_length", "50", TextArgumentParameter::PATTERN_DIGIT);
		$prefixLength->excludeButAllowIf($otuPickingMethod, "prefix_suffix");
		$suffixLength = new TextArgumentParameter("--suffix_length", "50", TextArgumentParameter::PATTERN_DIGIT);
		$suffixLength->excludeButAllowIf($otuPickingMethod, "prefix_suffix");

		$blastDb = new OldFileParameter("--blast_db", $this->project);
		$blastDb->excludeButAllowIf($otuPickingMethod, "blast");
		$minAlignedPercent = new TextArgumentParameter("--min_aligned_percent", "0.5", TextArgumentParameter::PATTERN_PROPORTION);
		$minAlignedPercent->excludeButAllowIf($otuPickingMethod, "blast");
		$maxEValue = new TextArgumentParameter("--max_e_value", "1e-10", TextArgumentParameter::PATTERN_NUMBER);
		$maxEValue->excludeButAllowIf($otuPickingMethod, "blast");

		$refSeqsFp = new OldFileParameter("--refseqs_fp", $this->project);
		$refSeqsFp->excludeButAllowIf($otuPickingMethod, "blast");
		$refSeqsFp->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$refSeqsFp->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$refSeqsFp->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$suppressNewClusters = new TrueFalseParameter("--suppress_new_clusters");
		$suppressNewClusters->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$suppressNewClusters->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$suppressNewClusters->excludeButAllowIf($otuPickingMethod, "usearch61_ref");

		$similarity = new TextArgumentParameter("--similarity", "0.97", TextArgumentParameter::PATTERN_PROPORTION);
		$similarity->excludeButAllowIf($otuPickingMethod, "cdhit");
		$similarity->excludeButAllowIf($otuPickingMethod, "blast");
		$similarity->excludeButAllowIf($otuPickingMethod, "usearch");
		$similarity->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$similarity->excludeButAllowIf($otuPickingMethod, "usearch61");
		$similarity->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$similarity->excludeButAllowIf($otuPickingMethod, "uclust");
		$similarity->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$wordLength = new TextArgumentParameter("--word_length", "12", TextArgumentParameter::PATTERN_DIGIT); // TODO dynamic default
		$wordLength->excludeButAllowIf($otuPickingMethod, "uclust");
		$wordLength->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$wordLength->excludeButAllowIf($otuPickingMethod, "usearch");
		$wordLength->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$wordLength->excludeButAllowIf($otuPickingMethod, "usearch61");
		$wordLength->excludeButAllowIf($otuPickingMethod, "usearch61_ref");


		$suppressPresortByAbundanceUclut = new TrueFalseParameter("--suppress_presort_by_abundance_uclust");
		$suppressPresortByAbundanceUclut->excludeButAllowIf($otuPickingMethod, "uclust");
		$suppressPresortByAbundanceUclut->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$optimalUclust = new TrueFalseParameter("--optimal_uclust");
		$optimalUclust->excludeButAllowIf($otuPickingMethod, "uclust");
		$optimalUclust->excludeButAllowIf($otuPickingMethod, "uclust_ref"); // TODO really?
		$exactUclust = new TrueFalseParameter("--exact_uclust");
		$exactUclust->excludeButAllowIf($otuPickingMethod, "uclust");
		$exactUclust->excludeButAllowIf($otuPickingMethod, "uclust_ref"); // TODO really?
		$userSort = new TrueFalseParameter("--user_sort");
		$userSort->excludeButAllowIf($otuPickingMethod, "uclust");
		$userSort->excludeButAllowIf($otuPickingMethod, "uclust_ref"); // TODO really?
		$stepwords = new TextArgumentParameter("--stepwords", "20", TextArgumentParameter::PATTERN_DIGIT);
		$stepwords->excludeButAllowIf($otuPickingMethod, "uclust");
		$stepwords->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$uclustOtuIdPrefix = new TextArgumentParameter("--uclust_otu_id_prefix", "denovo", TextArgumentParameter::PATTERN_NO_WHITE_SPACE); // TODO no whitespace
		$uclustOtuIdPrefix->excludeButAllowIf($otuPickingMethod, "uclust");
		$uclustOtuIdPrefix->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$suppressUclustStableSort = new TrueFalseParameter("--suppress_uclust_stable_sort");
		$suppressUclustStableSort->excludeButAllowIf($otuPickingMethod, "uclust");
		$suppressUclustStableSort->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$suppressUclustPrefilterExactMatch = new TrueFalseParameter("--suppress_uclust_prefilter_exact_match");
		$suppressUclustPrefilterExactMatch->excludeButAllowIf($otuPickingMethod, "uclust");
		$suppressUclustPrefilterExactMatch->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$saveUcFiles = new TrueFalseInvertedParameter("--save_uc_files");
		$saveUcFiles->excludeButAllowIf($otuPickingMethod, "uclust");
		$saveUcFiles->excludeButAllowIf($otuPickingMethod, "uclust_ref");

		$maxAccepts = new TextArgumentParameter("--max_accepts", "20", TextArgumentParameter::PATTERN_DIGIT); // TODO dynamic default
		$maxAccepts->excludeButAllowIf($otuPickingMethod, "uclust");
		$maxAccepts->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$maxAccepts->excludeButAllowIf($otuPickingMethod, "usearch61");
		$maxAccepts->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$maxRejects = new TextArgumentParameter("--max_rejects", "500", TextArgumentParameter::PATTERN_DIGIT); // TODO dynamic default
		$maxRejects->excludeButAllowIf($otuPickingMethod, "uclust");
		$maxRejects->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$maxRejects->excludeButAllowIf($otuPickingMethod, "usearch61");
		$maxRejects->excludeButAllowIf($otuPickingMethod, "usearch61_ref");

		$usearchFastCluster = new TrueFalseParameter("--usearch_fast_cluster"); // TODO forces usearch61_sort_method = long
				// TODO can't be used with --enable_rev_strand_matchign
		$usearchFastCluster->excludeButAllowIf($otuPickingMethod, "usearch");
		$usearchFastCluster->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$usearchFastCluster->excludeButAllowIf($otuPickingMethod, "usearch61");
		$usearchFastCluster->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$enableRevStrandMatch = new TrueFalseParameter("--enable_rev_strand_match");
		$enableRevStrandMatch->excludeButAllowIf($otuPickingMethod, "uclust");
		$enableRevStrandMatch->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$enableRevStrandMatch->excludeButAllowIf($otuPickingMethod, "usearch");
		$enableRevStrandMatch->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$enableRevStrandMatch->excludeButAllowIf($otuPickingMethod, "usearch61");
		$enableRevStrandMatch->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$fastOrThoroughUsearchClustering = $usearchFastCluster->linkTo($enableRevStrandMatch);
		$fastOrThoroughUsearchClustering->excludeButAllowIf($otuPickingMethod, "uclust");
		$fastOrThoroughUsearchClustering->excludeButAllowIf($otuPickingMethod, "uclust_ref");
		$fastOrThoroughUsearchClustering->excludeButAllowIf($otuPickingMethod, "usearch");
		$fastOrThoroughUsearchClustering->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$fastOrThoroughUsearchClustering->excludeButAllowIf($otuPickingMethod, "usearch61");
		$fastOrThoroughUsearchClustering->excludeButAllowIf($otuPickingMethod, "usearch61_ref");

		$percentIdError = new TextArgumentParameter("--percent_id_err", "0.97", TextArgumentParameter::PATTERN_PROPORTION);
		$percentIdError->excludeButAllowIf($otuPickingMethod, "usearch");
		$percentIdError->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$percentIdError->excludeButAllowIf($otuPickingMethod, "usearch61");
		$percentIdError->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$minSize = new TextArgumentParameter("--minsize", "4", TextArgumentParameter::PATTERN_DIGIT);
		$minSize->excludeButAllowIf($otuPickingMethod, "usearch");
		$minSize->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$minSize->excludeButAllowIf($otuPickingMethod, "usearch61");
		$minSize->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$abundanceSkew = new TextArgumentParameter("--abundance_skew", "2.0", TextArgumentParameter::PATTERN_NUMBER);
		$abundanceSkew->excludeButAllowIf($otuPickingMethod, "usearch"); // TODO exclude if suppress_chimera_detection
		$abundanceSkew->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$abundanceSkew->excludeButAllowIf($otuPickingMethod, "usearch61");
		$abundanceSkew->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$dbFilePath = new OldFileParameter("--db_filepath", $this->project);
		$dbFilePath->excludeButAllowIf($otuPickingMethod, "usearch");
		$dbFilePath->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$dbFilePath->excludeButAllowIf($otuPickingMethod, "usearch61");
		$dbFilePath->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$percIdBlast = new TextArgumentParameter("--perc_id_blast", "0.97", TextArgumentParameter::PATTERN_PROPORTION); 
		$percIdBlast->excludeButAllowIf($otuPickingMethod, "usearch");
		$percIdBlast->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$percIdBlast->excludeButAllowIf($otuPickingMethod, "usearch61");
		$percIdBlast->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		//new TrueFalseInvertedParameter("--de_novo_chimera_detection"), // TODO deprecated
		$suppressDeNovoChimeraDetection = new TrueFalseParameter("--suppress_de_novo_chimera_detection");
		$suppressDeNovoChimeraDetection->excludeButAllowIf($otuPickingMethod, "usearch");
		$suppressDeNovoChimeraDetection->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$suppressDeNovoChimeraDetection->excludeButAllowIf($otuPickingMethod, "usearch61");
		$suppressDeNovoChimeraDetection->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		//new TrueFalseInvertedParameter("--reference_chimera_detection"), // TODO deprecated
		$suppressReferenceChimeraDetection = new TrueFalseParameter("--suppress_reference_chimera_detection");
		$suppressReferenceChimeraDetection->excludeButAllowIf($otuPickingMethod, "usearch");
		$suppressReferenceChimeraDetection->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$suppressReferenceChimeraDetection->excludeButAllowIf($otuPickingMethod, "usearch61");
		$suppressReferenceChimeraDetection->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		//new TrueFalseInvertedParameter("--cluster_size_filtering"), // TODO deprecated
		$suppressClusterSizeFiltering = new TrueFalseParameter("--suppress_cluster_size_filtering");
		$suppressClusterSizeFiltering->excludeButAllowIf($otuPickingMethod, "usearch");
		$suppressClusterSizeFiltering->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$suppressClusterSizeFiltering->excludeButAllowIf($otuPickingMethod, "usearch61");
		$suppressClusterSizeFiltering->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$removeUsearchLogs = new TrueFalseParameter("--remove_usearch_logs");
		$removeUsearchLogs->excludeButAllowIf($otuPickingMethod, "usearch");
		$removeUsearchLogs->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$removeUsearchLogs->excludeButAllowIf($otuPickingMethod, "usearch61");
		$removeUsearchLogs->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$derepFullseq = new TrueFalseParameter("--derep_fullseq");
		$derepFullseq->excludeButAllowIf($otuPickingMethod, "usearch");
		$derepFullseq->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$derepFullseq->excludeButAllowIf($otuPickingMethod, "usearch61");
		$derepFullseq->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$nonChimerasRetention = new ChoiceParameter("--non_chimeras_retention", "union", array("union", "intersect"));
		$nonChimerasRetention->excludeButAllowIf($otuPickingMethod, "usearch");
		$nonChimerasRetention->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$nonChimerasRetention->excludeButAllowIf($otuPickingMethod, "usearch61");
		$nonChimerasRetention->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$minLen = new TextArgumentParameter("--minlen", "64", TextArgumentParameter::PATTERN_DIGIT);
		$minLen->excludeButAllowIf($otuPickingMethod, "usearch");
		$minLen->excludeButAllowIf($otuPickingMethod, "usearch_ref");
		$minLen->excludeButAllowIf($otuPickingMethod, "usearch61");
		$minLen->excludeButAllowIf($otuPickingMethod, "usearch61_ref");

		$usearch61SortMethod = new ChoiceParameter("--usearch61_sort_method", "abundance",
				array("abundance", "length", "None"));
		$usearch61SortMethod->excludeButAllowIf($otuPickingMethod, "usearch61");
		$usearch61SortMethod->excludeButAllowIf($otuPickingMethod, "usearch61_ref");
		$sizeOrder = new TrueFalseParameter("--sizeorder");
		$sizeOrder->excludeButAllowIf($usearch61SortMethod, "abundance");

		$threads = new TextArgumentParameter("--threads", "1.0", TextArgumentParameter::PATTERN_NUMBER);
		$threads->excludeButAllowIf($otuPickingMethod, "usearch61");

		array_push($this->parameters,
			new Label("Required Parameters"),
			$inputSeqsFilePath,
			new Label("Optional Parameters"),
			$triePrefilter,
			$prefixPrefilterLength,
			$otuPickingMethod,
			$clusteringAlgorithm,
			$maxCdhitMemory,
			$trieReverseSeqs,
			$prefixLength,
			$suffixLength,
			$blastDb,
			$minAlignedPercent,
			$maxEValue,
			$refSeqsFp,
			$suppressNewClusters,
			$similarity,
			$wordLength,
			$suppressPresortByAbundanceUclut,
			$optimalUclust,
			$exactUclust,
			$userSort,
			$stepwords,
			$uclustOtuIdPrefix,
			$suppressUclustStableSort,
			$suppressUclustPrefilterExactMatch,
			$saveUcFiles,
			$maxAccepts,
			$maxRejects,
			$fastOrThoroughUsearchClustering,
			$percentIdError,
			$minSize,
			$abundanceSkew,
			$dbFilePath,
			$percIdBlast,
			$suppressDeNovoChimeraDetection,
			$suppressReferenceChimeraDetection,
			$suppressClusterSizeFiltering,
			$removeUsearchLogs,
			$derepFullseq,
			$nonChimerasRetention,
			$minLen,
			$usearch61SortMethod,
			$sizeOrder,
			$threads,

			new Label("Ouput options"),
			new TrueFalseParameter("--verbose"),
			new NewFileParameter("--output_dir", "uclust_picked_otus") // TODO dynamic default
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
		ob_start();
		echo "<p>{$this->getScriptTitle()}</p><p>OTU stands for Operational Taxonomic Unit. This script attempts to group sequences into taxonomic units based on similarity.</p>";
		include "views/{$this->getHtmlId()}.html";
		return ob_get_clean();
	}

}
