<?php
echo "<div id=\"session_data\">{$this->getSessionData()}</div>\n";

echo "<h2>{$this->getSubtitle()}</h2>\n";

if ($this->hasResult()) {
	$class = $this->isResultError() ? " class=\"error\"" : "";
	echo "<div id=\"result\"{$class}>{$this->getResult()}</div>\n";
	echo "<hr/>";
}

echo "<div class=\"hideme\"><em>Instructions (<a id=\"instruction_controller\" onclick=\"hideMe(this);\">hide</a>):</em><div class=\"hideme\" id=\"instructions\">" .
   	$this->getInstructions() . "</div></div>\n";

if ($this->hasPastResults()) {
	echo "<hr/>";
	echo "<div id=\"past_results\" class=\"hideme\"><em>Past results (<a onclick=\"hideMe(this)\">hide</a>)</em><div class=\"hideme\">{$this->renderPastResults()}</div></div>";
}

$form = $this->getForm();
if ($form) {
	echo "<hr/>";
	echo "<div class=\"form\">{$form}</div>\n";
}
