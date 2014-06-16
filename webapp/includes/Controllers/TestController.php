<?php

namespace Controllers;

class TestController extends Controller {
	public function parseSession() {
		return;
	}
	public function parseInput() {
		if (isset($_GET['clean'])) {
			$this->clean();
		}
		return;
	}
	private function clean() {
		return;
	}
	public function getInstructions() {
		ob_start();

		echo "<label for=\"trigger1\">Hi there, I'm trigger 1: <input name=\"trigger1\" type=\"checkbox\"></label>";
		echo "<label for=\"trigger2\">Hi there, I'm trigger 2: <input name=\"trigger2\" type=\"checkbox\"></label>";
		echo "<label for=\"dependent\">Hi there, I'm a dependent: <input type=\"text\" name=\"dependent\"/></label>";
		echo "<script type=\"text/javascript\" src=\"parameter_relationships.js\"></script>";
		echo "<script type=\"text/javascript\">
var dependent = $('input[name=\"dependent\"]');
makeDependent(dependent);
dependent.allowOn('trigger1', false);
dependent.allowOn('trigger2', true);
var trigger1 = $('[name=\"trigger1\"]');
makeTrigger(trigger1);
var trigger2 = $('[name=\"trigger2\"]');
makeTrigger(trigger2);
dependent.listenTo(trigger1);
dependent.listenTo(trigger2);
			</script>";

		return ob_get_clean();
	}
}
