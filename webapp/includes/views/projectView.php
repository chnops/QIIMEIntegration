<?php

if (!isset($project)) {
	echo "<div class=\"error\">Unable to load project</div>";
}
else {
?>
<h3><?php echo $project->getName();?></h3>
<ul>
	<li>Owner: <?php echo $project->getOwner();?></li>
	<li>Unique id: <?php echo $project->getId();?></li>
</ul>
<hr/>
<?php
	$uploadedFiles = $project->retrieveAlluploadedFiles();
	if ($uploadedFiles) {
		echo "<h3>Uploaded Files:</h3>\n";
		foreach ($uploadedFiles as $fileType => $arrayOfFiles) {
			$fileTypeTitle = $this->project->getFileTypeFromHtmlId($fileType)->getName();
			echo "<h4>{$fileTypeTitle} Files</h4><ul>\n";
			foreach ($arrayOfFiles as $file) {
				echo "<li>" . htmlentities($file) . "</li>\n";
			}
			echo "</ul><hr class=\"small\"/>\n";
		}
	}
}
