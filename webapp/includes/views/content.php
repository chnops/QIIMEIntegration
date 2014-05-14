<?php echo "<h2>{$this->getSubtitle()}</h2>\n";

if ($this->hasImmediateResult()) {
	echo "<div class=\"result immediate\">" . htmlentities($this->getImmediateResult()) . "</div>\n";
	echo "<hr/>";
}

if ($this->hasPastResults()) {
	echo "<div class=\"result past\">" . htmlentities($this->getPastResults()) . "</div>\n";
	echo "<hr/>";
}


echo "<p><em>Instructions (<a id=\"instruction_controller\" onclick=\"toggleInstruction();\">hide</a>):</em></p><span id=\"instructions\">" .
   	$this->getInstructions() . "</span>\n";

echo "<hr/>";

echo $this->getForm() . "\n";
