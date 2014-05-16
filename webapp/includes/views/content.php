<?php
echo "<div id=\"session_data\">{$this->getSessionData()}</div>\n";

echo "<h2>{$this->getSubtitle()}</h2>\n";

if ($this->hasResult()) {
	$class = $this->isResultError() ? " class=\"error\"" : "";
	echo "<div id=\"result\"{$class}>{$this->getResult()}</div>\n";
	echo "<hr/>";
}

echo "<p><em>Instructions (<a id=\"instruction_controller\" onclick=\"toggleInstruction();\">hide</a>):</em></p><span id=\"instructions\">" .
   	$this->getInstructions() . "</span>\n";
echo "<hr/>";

if ($this->hasPastResults()) {
	echo $this->renderPastResults();
	echo "<hr/>";
}

echo "<div class=\"form\">{$this->getForm()}</div>\n";
