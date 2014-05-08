#!/bin/bash

#########################################################
#														#
#	Author: Aaron Sharp									#
#	Contact: sharp.aaron.r@gmail.com					#
#	Date begun: 05/06/2014								#
#	Lab: Dr. Hofmockel, EEOB, Iowa State University		#
#														#
#########################################################

RETURN_CODE=0
PROFILE_FP=/macqiime/configs/bash_profile.txt

echo "===================================================================="
echo "Beginning QIIME Operational Taxonomic Unit (OTU) diversity analysis."
echo "--------------------------------------------------------------------"

## TODO universal, consistent treatment of errors
printf "Sourcing necessary environment variables ... "
source $PROFILE_FP 2> /dev/null
if [ $? -ne 0 ]
	then
		printf "FAIL\n\tSourcing failed; unable to read or execute file: $PROFILE_FP\n"
		RETURN_CODE=3
		exit $RETURN_CODE
		## TODO still "finish" script by printing execution complete
	else
		printf "OK\n"
fi

MAP_FP=$1
if [ ! $MAP_FP ]
then
	printf "FAIL\n\tNo map file specified.\n"
	RETURN_CODE=4
	exit $RETURN_CODE
fi

## TODO mapping_output might already exist in the working directory
printf "Validating map file ($MAP_FP) ... "
MAP_VALIDATION_RESULT=`validate_mapping_file.py -m $MAP_FP -s -o mapping_output`
if [ "$MAP_VALIDATION_RESULT" != "No errors or warnings were found in mapping file." ]
then
	printf "FAIL\n\t$MAP_VALIDATION_RESULT\n"
	RETURN_CODE=5
	exit $RETURN_CODE
else
	rm -r mapping_output
	printf "OK\n"
fi

echo "--------------------------------------------------------------------"

## TODO results might already exist in the working directory
RESULT_DIR=results
mkdir $RESULT_DIR

## TODO incorporate getopts()
## TODO include sequence quality information
SEQUENCE_FP=$2
if [ ! $SEQUENCE_FP ]
then
	printf "FAIL\n\tNo sequence file specified.\n"
	RETURN_CODE=6
	exit $RETURN_CODE
fi

## TODO possibly include identification of chimeric sequences

## TODO splitting_output might already exist in the working directory
## TODO a separate quality file is uneccesary if sequences are in fastq format, but a different python script must be used
## TODO possibly implement reverse primer deletion here
printf "Splitting multiplexed libraries by barcode ... "
LIBRARY_SPLITTING_RESULT=`split_libraries.py -f $SEQUENCE_FP -m $MAP_FP -o splitting_output`
if [ $LIBRARY_SPLITTING_RESULT ]
then
	printf "FAIL\n\tUnable to split libraries ($LIBRARY_SPLITTING_RESULT)\n"
	RETURN_CODE=7
	exit $RETURN_CODE
else
	printf "OK\n"
fi
mv splitting_output/histograms.txt $RESULT_DIR/sequence_length_histogram.txt
mv splitting_output/split_library_log.txt $RESULT_DIR/
## TODO split_sequences.fna might already exist in the working directory
mv splitting_output/seqs.fna split_sequences.fna
rmdir splitting_output

echo "--------------------------------------------------------------------"

## TODO potentially implement de-noising at this step (would require .sff file)
#####################################################
#													#
#	Here are all the commands normally				# 
#		completed by the macro-script 				#
#		pick_de_novo_otus.py						#
#													#
#	pick_otus.py -i split_sequences.fna -o otus/uclust_picked_otus 
#	pick_rep_set.py -i otus/uclust_picked_otus/seqs_otus.txt -f ../qiime_overview_tutorial/split_library_output/seqs.fna -l otus/rep_set//seqs_rep_set.log -o otus/rep_set//seqs_rep_set.fasta 
#	assign_taxonomy.py -o otus/uclust_assigned_taxonomy -i otus/rep_set//seqs_rep_set.fasta 
#	make_otu_table.py -i otus/uclust_picked_otus/seqs_otus.txt -t otus/uclust_assigned_taxonomy/seqs_rep_set_tax_assignments.txt -o otus/otu_table.biom 
#	align_seqs.py -i otus/rep_set//seqs_rep_set.fasta -o otus/pynast_aligned_seqs 
#	filter_alignment.py -o otus/pynast_aligned_seqs -i otus/pynast_aligned_seqs/seqs_rep_set_aligned.fasta 
#	make_phylogeny.py -i otus/pynast_aligned_seqs/seqs_rep_set_aligned_pfiltered.fasta -o otus/rep_set.tre 
#													#
#####################################################

## TODO redirect errors to a file, then check its line count*
## TODO otus may already exist in the working directory
printf "Picking OTUs based on cross-sample sequence similarity ... "
OTU_SELECTION_RESULT=`pick_otus.py -i split_sequences.fna -o otus/uclust_picked_otus`
if [ $OTU_SELECTION_RESULT ]
then
	printf "FAIL\n\tUnable to pick OTUs ($OTU_SELECTION_RESULT)\n"
	RETURN_CODE=8
	exit $RETURN_CODE
else
	printf "OK\n"
fi
mv otus/uclust_picked_otus/split_sequences_clusters.uc $RESULT_DIR/uclust_clusters.log
mv otus/uclust_picked_otus/split_sequences_otus.log $RESULT_DIR/uclust_otus.log
## TODO otus.txt may already exist in the working directory
mv otus/uclust_picked_otus/split_sequences_otus.txt otus.txt
rmdir otus/uclust_picked_otus

printf "Selecting representative sequence from each OTU ... "
mkdir otus/rep_set
## TODO files that are used multiple times (otus.txt, split_sequences.fna) should be referred to with variable names
REPRESENTATIVE_SELECTION_RESULT=`pick_rep_set.py -i otus.txt -f split_sequences.fna -l otus/rep_set/seqs_rep_set.log -o otus/rep_set/seqs_rep_set.fasta`
## TODO not effective error checking*
if [ $REPRESENTATIVE_SELECTION_RESULT ]
then
	printf "FAIL\n\tUnable to select representative OTUs ($OTU_SELECTION_RESULT)\n"
	RETURN_CODE=9
	exit $RETURN_CODE
else
	printf "OK\n"
fi
mv otus/rep_set/seqs_rep_set.log $RESULT_DIR/uclust_representative_sequences.log
## TODO representative_sequences.fasta may already exist, and it should be a variable, not a literal
mv otus/rep_set/seqs_rep_set.fasta representative_sequences.fasta
rmdir otus/rep_set/

echo "--------------------------------------------------------------------"

## TODO might be false
PERFORM_PHYLOGENY=true
if [ $PERFORM_PHYLOGENY ]
then
	echo '--------------Performing steps for phylogeny analysis---------------'

	## TODO make default options (intentionally being used) explicit in script
	printf "Aligning representative OTU sequences ... "
	ALIGN_SEQUENCE_RESULT=`align_seqs.py -i representative_sequences.fasta -o otus/alignment`
	## TODO check otus/alignment/representative_sequences_failures.fasta, print warning**
	if [ $ALIGN_SEQUENCE_RESULT ]
	then
		printf "FAIL\n\tUnable to align representative OTU sequences ($ALIGN_SEQUENCE_RESULT)\n"
		printf "\tSkipping remaining phylogeny steps"
	else
		printf "OK\n"

		mv otus/alignment/representative_sequences_log.txt $RESULT_DIR/rep_seq_alignment.log
		## TODO only run this command if the file is empty**
		rm otus/alignment/representative_sequences_failures.fasta

		## TODO lanemask is a default; should be explicit
		printf "Filtering alignment ... "
		FILTER_SEQUENCE_RESULT=`filter_alignment.py -i otus/alignment/representative_sequences_aligned.fasta -o otus/alignment`

		if [ $FILTER_SEQUENCE_RESULT ]
		then
			printf "FAIL\n\tUnable to filter sequence alignment ($FILTER_SEQUENCE_RESULT)\n"
			printf "\tSkipping remaining phylogeny steps"
		else
			printf "OK\n"
		
			rm otus/alignment/representative_sequences_aligned.fasta
			## TODO filtered_alignment.fasta may already exist in the working directory
			mv otus/alignment/representative_sequences_aligned_pfiltered.fasta filtered_alignment.fasta

			## TODO -t and -r are both defaults
			## TODO filtered_alignment.fasta should be a variable
			printf "Creating phylogeny from filtered alignment ... "
			MAKE_PHYLOGENY_RESULT=`make_phylogeny.py -i filtered_alignment.fasta -l $RESULT_DIR/phylogeny.log -o $RESULT_DIR/phylogeny.tre`
			if [ $MAKE_PHYLOGENY_RESULT ]
			then
				printf "FAIL\n\tUnable to make phylogeny ($MAKE_PHYLOGENY_RESULT)\n"
			else
				printf "OK\n"

				## TODO should be a variable, maybe
				rmdir otus/alignment
				mv filtered_alignment.fasta $RESULT_DIR
			fi
		fi
	fi
	echo "--------------------------------------------------------------------"
fi

## TODO make this step parallel, because it is so long
## TODO this step isn't necessary; possibly remove it
printf "Assigning OTUs to known taxonomies (this may be an extra long step) ... "
## TODO remove this comment
#TAXONOMY_ASSIGNMENT_RESULT=`assign_taxonomy.py -i representative_sequences.fasta -o taxonomies`
## TODO remove this line
TAXONOMY_ASSIGNMENT_RESULT=""
if [ $TAXONOMY_ASSIGNMENT_RESULT ]
then
	printf "FAIL\n\tUnable to assign taxonomies ($TAXONOMY_ASSIGNMENT_RESULT)\n"
	RETURN_CODE=10
	exit $RETURN_CODE
else
	printf "OK\n"

	mv taxonomies/seqs_rep_set_tax_assignments.log $RESULT_DIR/taxonomy_assignment.log
	## TODO move taxonomies/seqs_rep_set_tax_assignments.txt to the results or working directory***
	rmdir taxonomies
fi

printf "Creating OTU table ... "
## TODO rename taxonomies/seqs_rep_set_tax_assignments.txt***
MAKE_OTU_TABLE_RESULT=`make_otu_table.py -i otus.txt -o otu_table.biom -t taxonomies/seqs_rep_set_tax_assignments.txt`
if [ $MAKE_OTU_TABLE_RESULT ]
then
	printf "FAIL\n\tUnable to create OTU table\n"
	RETURN_CODE=11
	exit $RETURN_CODE
else
	printf "OK\n"
fi

echo "--------------------------------------------------------------------"
echo "----CHECKPOINT: OTU table created; begin statistic visualization----"

rmdir otus

echo "--------------------------------------------------------------------"
echo "Execution complete."
echo "===================================================================="
## TODO create a separate folder for the results of this run.  Copy the input files into that folder.  Make a link called most recent, pointing to that run.
## TODO utilize functions
## TODO create GUI
## TODO implement check-points be creating un-writeable files
exit $RETURN_CODE
