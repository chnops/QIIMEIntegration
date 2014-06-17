<?php

echo "<div id=\"session_data\">{$this->renderSessionData()}</div>\n";
echo "<h2>{$this->getSubtitle()}</h2>\n";

$result = $this->getResult();
if ($result) {
	$class = $this->isResultError() ? " class=\"error\"" : "";
	echo "<div id=\"result\"{$class}>{$result}</div><br/>\n";
}

$instructions = $this->renderInstructions();
if ($instructions) {
	echo "<div id=\"instructions\" class=\"hideme\"><em>Instructions (<a id=\"instruction_controller\" onclick=\"hideMe(this);\">hide</a>):</em><div class=\"hideme\">" .
   		$instructions . "</div></div>\n";
}

$pastResults = $this->renderPastResults();
if ($pastResults) {
	echo "<div id=\"past_results\" class=\"hideme\"><em>Past results (<a onclick=\"hideMe(this)\">hide</a>)</em><div class=\"hideme\">{$pastResults}</div></div><br/>";
}

$form = $this->renderForm();
if ($form) {
	echo "<div class=\"form\">{$form}</div>\n";
}
