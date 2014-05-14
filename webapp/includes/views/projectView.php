<?php

if (!isset($project)) {
	echo "<div class=\"error\">Unable to load project</div>";
}
else {
?>
<div class="project">
<h3><?php echo $project->getName();?></h3>
<ul>
	<li>Owner: <?php echo $project->getOwner();?></li>
	<li>Unique id: <?php echo $project->getId();?></li>
</ul>
</div>
<?php
}
