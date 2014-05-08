QIIMEIntegration
================

I am working on incorporating QIIME into a workspace-specific pipeline. I keep notes and useful scripts here.

1. Installation
 * I used MacQIIME (http://www.wernerlab.org/software/macqiime) for easy installation. Out of box, most everything worked fine. 
 * Because I work with paired-end sequencing data, I included fastq-join as well (http://www.wernerlab.org/software/macqiime/add-fastq-join-to-macqiime-1-8-0/Add_ea-tools_to_MacQIIME-1.8.0.tgz?attredirects=0&d=1)

2. Automation
As an initial step, I created a script (run_qiime_analysis.sh) that will perform some of QIIMEs fundamental tasks with default parameters
 a. Source QIIME bash environment variables
 b. Check mapping file format
 c. De-multiplex sequences
 d. Trim sequences based on quality
 e. Select OTUs based on sequence similarity
 f. Select representative sequences for each OTU
 g. Optionally perform steps for phylogeny analysis (align sequences, filter alignment, create tree)
 h. Optionally perform taxonomy assignment
 i. Creat OTU table, the starting point for many of the QIIME analyses

3. Customization
The format checking, de-multiplexing, quality trimming, and OTU selection are probably central to any analyses, therefore they are invariably performed. At the moment, they use mostly default parameters; however, they will allow customization once complete. Additionally, the choice to perform phylogenetic analysis is hard coded to yes. This must be changed before the project is complete, and is marked as a TODO item in the script. Like the phylogeny analysis, the taxonomy assignment is hard coded to be performed.

4. Next step
Make long term design decisions and address TODO items in the script. Also, become familiar with optional statistical analyses of the OTU table.
