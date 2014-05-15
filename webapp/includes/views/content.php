<?php echo "<h2>{$this->getSubtitle()}</h2>\n";

if ($this->hasImmediateResult()) {
	echo "<div class=\"result immediate\">{$this->getImmediateResult()}</div>\n";
	echo "<hr/>";
}

if ($this->hasPastResults()) {
	echo "<div class=\"result past\">{$this->getPastResults()}</div>\n";
	echo "<hr/>";
}


echo "<p><em>Instructions (<a id=\"instruction_controller\" onclick=\"toggleInstruction();\">hide</a>):</em></p><span id=\"instructions\">" .
   	$this->getInstructions() . "</span>\n";

echo "<hr/>";

echo "<div class=\"form\">{$this->getForm()}</div>\n";
