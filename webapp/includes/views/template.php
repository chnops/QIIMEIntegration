<!DOCTYPE html>
<html>
<head>
	<title><?php echo $this->getSubTitle() ?></title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="style.css">
<style><?php echo $this->renderSpecificStyle()?></style>
<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="javascript.js"></script>
<script type="text/javascript">
<?php echo $this->renderSpecificScript()?>
</script>
<?php foreach ($this->getScriptLibraries() as $lib):?>
<script type="text/javascript" src="<?php echo $lib?>"></script>
<?php endforeach?>
</head>
<body>
<div id="banner"><h1><?php echo $this->title  ?></h1><h2><?php echo $this->getSubTitle() ?></h2></div>
<div id="navigation"><table><tr>
<?php
$steps = $this->getWorkflow()->getSteps();
if (isset($steps[$this->step])) {
	$steps[$this->step] = "<strong>--" . $steps[$this->step] . "--</strong>";
}
foreach ($steps as $key => $step) {
	echo "<td><a href=\"index.php?step=$key\">{$step}</a></td>";
}
?>
</tr></table></div>
<div id="content"><?php echo $this->getContent() ?></div>
<?php $help = $this->renderHelp();
if ($help):?>
<div id="help"><em>Help (<a onclick="hideMe($(this).parent().next())">hide</a>)</em><div><?php echo $help;?></div></div>
<?php endif;
$postHelp = $this->getExtraHtml('post_help');
if ($postHelp) {
echo "<div id=\"post_help\">{$postHelp}</div>";
}?>
<div id="footer"><h3>Please remember to <a href="http://qiime.org" target="_blank">cite QIIME</a></h3></div> 
</body>
</html>
