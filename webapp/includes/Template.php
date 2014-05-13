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
		a:link {color:#003366;text-decoration:none;font-style:italic;}
		a:hover {background-color:#ffffcc;text-decoration:underline;}
	</style>
</head>
<body>
<div id="banner"><h1><?php echo $this->title  ?></h1><h2><?php echo $this->subTitle ?></h2></div>
<div id="navigation"><table><tr>
<?php
$steps = array(
	"login" => "Login",	
	"select" => "Select/create project",
	"upload" => "Upload files",
	"make_otu" => "Create OTU table",
	"make_phylogeny" => "Perform phylogeny analysis [optional]",
	"view" => "View results");
if (isset($_SESSION['username'])) {
	$steps['login'] = "Login (" . htmlentities($_SESSION['username']) . ")";
}
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
