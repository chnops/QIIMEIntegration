<!DOCTYPE html>
<html>
<head>
	<title><?php echo $this->subTitle ?></title>
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
		div#content,div#help{background-color:#ffcc66;border:2.5px solid;margin:.25em .5em;padding:.5em;}
		div#content{width:67%;float:left;}
		div#help{width:27%;float:right;}
		div#content div.form{margin-left:2.5em;text-align:left;width:45%;border:2px #999966 ridge;padding:1em;}
		div.file_example{border:1px solid;background-color:#999966;overflow:auto;font-family:monspace;padding:.25em;}
		a{color:#003366;cursor:pointer;}
		a:link {color:#003366;text-decoration:none;font-style:italic;}
		a:hover {background-color:#ffffcc;text-decoration:underline;}
		label{display:block;margin:.5em 0em;}
		input[disabled]{background-color:#999966;}
		hr.small{width:25%;margin-left:0px;}
	</style>
<script type="text/javascript">
	function toggleInstruction() {
		var control = document.getElementById('instruction_controller');
		var body = document.getElementById('instructions');
		if (control.innerHTML == "hide") {
			control.innerHTML = "show";
			body.style.display = "none";
		}
		else {
			control.innerHTML = "hide";
			body.style.display = "inline";
		}
	}	
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
<div id="help"><p><?php echo $this->help ?></p></div>
<div id="content"><p><?php echo $this->content ?></p></div>
<div id="footer"><h3>Please remember to <a href="http://qiime.org" target="_blank">cite QIIME</a></h3></div> 
</body>
