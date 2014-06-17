<!DOCTYPE html>
<html ondblclick="
var anchor_pos = $('div.form').position();
event = jQuery.event.fix(event);
$('#parameter_help').css('left', event.pageX - anchor_pos.left).css('top',event.pageY - anchor_pos.top);
">
<head>
	<title><?php echo $this->getSubTitle() ?></title>
	<meta charset="UTF-8">
	<?php
$darkBackground = '#999966'; // dark grey
$internalBorder = '#666633'; // greyish green
$neutral = '#ffcc66'; // yellowish
$neutralPale = '#ffffcc'; // yellowish-white
$striking = '#cc0000'; // red
$text = '#330000'; // close to black
$error = '#cc6600'; // orange
$link = '#003366'; // bluish
	?>
	<style>
		body {padding:0px;margin:0px;background-color:<?php echo $darkBackground?>;color:<?php echo $text?>}
		body *{border-style:solid;border-width:0px}
		div#banner,div#footer,div#navigation{background-color:<?php echo $striking?>;width:97%;border-width:1.5px;float:left;margin:.25em .5em;padding:0em .5em;}
		div#banner{margin-bottom:0em;}
		div#banner h1, div#banner h2{float:left;display:inline;}
		div#banner h2{float:right;font-style:italic;}
		div#navigation{background-color:<?php echo $neutral?>;border-top:none;margin-top:0em}
		div#navigation table{border-width:0px 1px;margin-right:auto;margin-left:auto;border-color:<?php echo $internalBorder?>}
		div#navigation table td{border-width:0px 0px 0px 1px;padding:0em .5em;border-color:<?php echo $internalBorder?>}
		div#navigation table td:first-child {border-left:none}

		div#content,div#help{background-color:<?php echo $neutral?>;border-width:1.5px;margin:.25em .5em;padding:1em;}
		div#content{width:65%;float:left;display:inline;margin-left:1.5em}
		div#help{width:25%;float:left;display:inline}
		div#session_data{border-width:0px 0px 1px 1px;border-color<?php echo $internalBorder?>;font-size:.9em;font-style:italic;display:inline-block;padding-left:.25em}
		div#content h2{margin-bottom:.5em}
		div#instructions {margin-bottom:1em}
		div#content div.form,div#result,div#past_results{border-width:1px;border-color:<?php echo $internalBorder?>;display:inline-block;margin-left:2.5em;padding:1em;overflow:auto;max-width:90%}
		div#result{background-color:<?php echo $neutralPale?>}
		div#result.error{background-color:<?php echo $error?>}

		div.file_example{border-width:1px;background-color:<?php echo $darkBackground?>;overflow:auto;font-family:monspace;padding:.25em;white-space:pre;padding:.5em;margin:.5em;}
		div.hideable{display:none;}
		a{color:<?php echo $link?>;cursor:pointer;}
		a:link {color:<?php echo $link?>;text-decoration:none;font-style:italic;}
		a:hover {background-color:<?php echo $neutralPale?>;text-decoration:underline;}
		a.button{font-style:normal;background-color:<?php echo $neutralPale?>;border:2px <?php echo $internalBorder?> outset;padding:.25em;}
		a.button:active {border:2px <?php echo $internalBorder?> inset;}
		label{display:block;margin:.5em 0em;}
		button{background-color:<?php echo $neutralPale?>;border:2px <?php echo $internalBorder?> outset}
		button:active{border:2px <?php echo $internalBorder?> inset}
		input,select{background-color:<?php echo $neutralPale?>;}
		input[disabled]{background-color:<?php echo $darkBackground?>;}
		select[size]{padding:.5em .5em 1.5em .5em}
		optgroup.big{font-size:1.25em;font-weight:bold}

		<?php echo $this->renderSpecificStyle()?>

		.accordion{margin:.75em;padding:0em}	
		.accordion h3,.accordion h4,.accordion div {outline:none;padding:.25em .5em;}
		.accordion h3,.accordion h4{background-color:#ffcc66;margin-bottom:0em;border-style:solid;border-width:1px 2px}
		.accordion h3,.accordion h4:not(:first-child){margin-top:0em}
		.accordion div{background-color:#ffffcc;margin:0em;border-style:solid;border-width:0px 2px}
		.accordion h3,.accordion h4:first-child{border-width:2px 2px 1px 2px}
		.accordion div:last-child{border-width:1px 2px 2px 2px}
		.draggable{padding:.5em;background-color:#ffffcc;border:1px solid}
	</style>
<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript">
function hideMe(trigger) {
	var triggerObj = $(trigger);
	var responseObj = triggerObj.parents('.hideme').find('.hideme');
	if (triggerObj.html() == "hide") {
		triggerObj.html("show");
		responseObj.css('display', "none");
	}
	else {
		triggerObj.html("hide");
		responseObj.css('display', "block");
	}
}	
var hideableFields = [];
var displayedHideableId = "";
function displayHideables(hideableToDisplayId) {
	for (var i = 0; i < hideableFields.length; i++) {
		var hideableToDisplay = document.getElementById(hideableFields[i] + "_" + hideableToDisplayId);
		var displayedHideable = document.getElementById(hideableFields[i] + "_" + displayedHideableId);
		if (displayedHideable) displayedHideable.style.display="none";
		if (hideableToDisplay) hideableToDisplay.style.display="block";
	}
	displayedHideableId = hideableToDisplayId;
}
function paramHelp(text) {
	$('#parameter_help').html("Parameter help: " + text);
}
	$(function() {
		$('.accordion').accordion({
		collapsible: true,
		}); 
		$('.draggable').draggable({
			scroll: false
		});
	})
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
	$steps[$this->step] = "<strong>" . $steps[$this->step] . "</strong>";
}
foreach ($steps as $key => $step) {
	echo "<td><a href=\"index.php?step=$key\">{$step}</a></td>";
}
?>
</tr></table></div>
<div id="content"><?php echo $this->getContent() ?></div>
<?php $help = $this->renderHelp();
if ($help):?>
<div id="help" class="hideme"><em>Help (<a onclick="hideMe(this)">hide</a>)</em><div class="hideme"><?php echo $help;?></div></div>
<?php endif; ?>
<div id="footer"><h3>Please remember to <a href="http://qiime.org" target="_blank">cite QIIME</a></h3></div> 
</body>
</html>
