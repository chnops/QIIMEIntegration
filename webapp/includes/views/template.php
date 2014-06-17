<!DOCTYPE html>
<html ondblclick="
var anchor_pos = $('div.form').position();
event = jQuery.event.fix(event);
$('#parameter_help').css('left', event.pageX - anchor_pos.left).css('top',event.pageY - anchor_pos.top);
">
<head>
	<title><?php echo $this->subTitle ?></title>
	<meta charset="UTF-8">
	<style>
		body {padding:0px;margin:0px;background-color:#999966;color:#330000;}
		div#banner,div#footer,div#navigation{background-color:#cc0000;width:97%;border:2.5px solid;float:left;margin:.25em .5em;padding:0em .5em;}
		div#banner{margin-bottom:0em;}
		div#banner h1, div#banner h2{float:left;display:inline;}
		div#banner h2{float:right;font-style:italic;}
		div#navigation{background-color:#ffcc66;border-top:none;margin-top:0em}
		div#navigation table{border-left:1px solid #999966;border-right:1px solid #999966;margin-right:auto;margin-left:auto;}
		div#navigation table td{border-left:1px solid #999966;padding:0em .5em;}
		div#navigation table td:first-child {border-left:none}
		div#content,div#help{background-color:#ffcc66;border:3px solid;margin:.25em .5em;padding:.5em;}
		div#content{width:65%;float:left;display:inline}
		div#help{width:25%;float:left;display:inline}
		div#content div.form{margin-left:2.5em;border:2px #999966 ridge;padding:1em;display:inline-block}
		div#session_data{margin-left:0px;margin-right:auto;border-style:solid;border-color:#999966;border-width:0px 0px 1px 1px;font-size:.9em;font-style:italic;display:inline-block;padding-left:.25em}
		div#result{border:2px #999966 ridge;display:inline-block;margin-left:2.5em;padding:.25em;background-color:#ffffcc}
		div#result.error{background-color:#cc6600}
		div#past_results{display:inline-block;background-color:#ffffcc;padding:0em .5em;border:1px #999966 solid}
		div#past_results,div#result{overflow:auto;max-width:90%}
		div.file_example{border:1px solid;background-color:#999966;overflow:auto;font-family:monspace;padding:.25em;white-space:pre;padding:.5em;margin:.5em;}
		div.script_form input[type="text"],div.script_form input[type="file"],select{display:block}
		div.hideable{display:none;}
		a{color:#003366;cursor:pointer;}
		a:link {color:#003366;text-decoration:none;font-style:italic;}
		a:hover {background-color:#ffffcc;text-decoration:underline;}
		a.button{font-style:normal;background-color:#ffffcc;border:2px outset;padding:.25em;}
		a.button:active {border:2px inset;}
		label{display:block;margin:.5em 0em;}
		button{background-color:#ffffcc;}
		input,select{background-color:#ffffcc;}
		input[disabled]{background-color:#999966;}
		hr.small{width:25%;margin-left:0px;}
		select[size]{padding:.5em .5em 1.5em .5em}
		optgroup.big{font-size:1.25em;font-weight:bold}
		p.conditional_requirement{text-decoration:underline;display:none}
		table.either_or{border:1px solid #999966;display:inline-block;padding:.25em}
		table.either_or td{padding:.25em;text-align:center}
		table.either_or tbody tr:first-child td {border-bottom:1px solid #999966}
		table.either_or td:not(:first-child) {border-left:1px solid #999966}
	
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
</script>
</head>
<body>
<div id="banner"><h1><?php echo $this->title  ?></h1><h2><?php echo $this->subTitle ?></h2></div>
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
<div id="content"><?php echo $this->content ?></div>
<div id="help" class="hideme"><em>Help (<a onclick="hideMe(this)">hide</a>)</em><div class="hideme"><?php echo $this->help ?></div></div>
<div id="footer"><h3>Please remember to <a href="http://qiime.org" target="_blank">cite QIIME</a></h3></div> 
</body>
</html>
