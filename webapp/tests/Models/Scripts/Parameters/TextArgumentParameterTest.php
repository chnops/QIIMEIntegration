<?php

namespace Models\Scripts\Parameters;

class TextArgumentParameterTest extends \PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		error_log("TextArgumentParameterTest");
	}

	private $name = "--name";
	private $value = "value";
	private $pattern = TextArgumentParameter::PATTERN_ANYTHING_GOES;
	private $object = NULL;
	public function setUp() {
		$this->object = new TextArgumentParameter($this->name, $this->value, $this->pattern);
	}

	/**
	 * @covers TextArgumentParameter::__construct
	 */
	public function testConstructor() {
		$expecteds = array(
			"name" => $this->name,
			"value" => $this->value,
			"pattern" => $this->pattern,
		);
		$actuals = array();

		$this->object = new TextArgumentParameter($this->name, $this->value, $this->pattern);

		$actuals['name'] = $this->object->getName();
		$actuals['value'] = $this->object->getValue();
		$actuals['pattern'] = $this->object->getExpectedPattern();
		$this->assertEquals($expecteds, $actuals);
	}

	/**
	 * @covers TextArgumentParameter::getExpectedPattern
	 */
	public function testGetPattern() {
		$expected = $this->pattern;

		$actual = $this->object->getExpectedPattern();

		$this->assertequals($expected, $actual);
	}

	/**
	 * @covers TextArgumentParameter::setExpectedPattern
	 */
	public function testSetPattern() {
		$expected = "new pattern";

		$this->object->setExpectedPattern($expected);

		$actual = $this->object->getExpectedPattern();
		$this->assertequals($expected, $actual);
	}

	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_valueIsFalse() {
		$expecteds = array(
			"digit" => true,
			"number" => true,
			"proportion" => true,
			"no_white_space" => true,
			"anything_goes" => true,
		);
		$actuals = array();
		$keys = array_keys($expecteds);
		$patterns = array(
			TextArgumentParameter::PATTERN_DIGIT,
			TextArgumentParameter::PATTERN_NUMBER,
			TextArgumentParameter::PATTERN_PROPORTION,
			TextArgumentParameter::PATTERN_NO_WHITE_SPACE,
			TextArgumentParameter::PATTERN_ANYTHING_GOES,
		);
		$falseyValues = array(false, "", 0, array());
		for($i = 0; $i < count($keys); $i++) {
			$value = $falseyValues[$i % count($falseyValues)];
			$this->object->setExpectedPattern($patterns[$i]);
			
			$actuals[$keys[$i]] = $this->object->isValueValid($value);

		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_digit_valid() {
		$expecteds = array();
		$actuals = array();
		$inputs = array(
			"one" => 1,
			"two" => 23,
			"nine" => 999999999,
			"string" => "42",
		);
		$this->object->setExpectedPattern(TextArgumentParameter::PATTERN_DIGIT);
		foreach (array_keys($inputs) as $key) {
			$expecteds[$key] = 1;

			$actuals[$key] = $this->object->isValueValid($inputs[$key]);

		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_digit_invalid() {
		$expecteds = array();
		$actuals = array();
		$inputs = array(
			"negative" => -1,
			"letter_precedes" => "a23",
			"letter_follows" => "23a",
			"white_space" => "9999 99999",
		);
		$this->object->setExpectedPattern(TextArgumentParameter::PATTERN_DIGIT);
		foreach (array_keys($inputs) as $key) {
			$expecteds[$key] = 0;

			$actuals[$key] = $this->object->isValueValid($inputs[$key]);

		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_number_valid() {
		$inputs = array(
			"positive_noDecimal_noSci" => "123",
			"positive_noDecimal_capitalSci_positive" => "123E456",
			"positive_noDecimal_capitalSci_negative" => "123E-456",
			"positive_noDecimal_lowerSci_positive" => "123e456",
			"positive_noDecimal_lowerSci_negative" => "123e-456",
			"positive_decimal_noSci" => "12.3",
			"positive_decimal_capitalSci_positive" => "12.3E456",
			"positive_decimal_capitalSci_negative" => "12.3E-456",
			"positive_decimal_lowerSci_positive" => "12.3e456",
			"positive_decimal_lowerSci_negative" => "12.3e-456",
			"positive_onlyDecimal_noSci" => ".123",
			"positive_onlyDecimal_capitalSci_positive" => ".123E456",
			"positive_onlyDecimal_capitalSci_negative" => ".123E-456",
			"positive_onlyDecimal_lowerSci_positive" => ".123e456",
			"positive_onlyDecimal_lowerSci_negative" => ".123e-456",
			"negative_noDecimal_noSci" => "-123",
			"negative_noDecimal_capitalSci_positive" => "-123E456",
			"negative_noDecimal_capitalSci_negative" => "-123E-456",
			"negative_noDecimal_lowerSci_positive" => "-123e456",
			"negative_noDecimal_lowerSci_negative" => "-123e-456",
			"negative_decimal_noSci" => "-12.3",
			"negative_decimal_capitalSci_positive" => "-12.3E456",
			"negative_decimal_capitalSci_negative" => "-12.3E-456",
			"negative_decimal_lowerSci_positive" => "-12.3e456",
			"negative_decimal_lowerSci_negative" => "-12.3e-456",
			"negative_onlyDecimal_noSci" => "-.123",
			"negative_onlyDecimal_capitalSci_positive" => "-.123E456",
			"negative_onlyDecimal_capitalSci_negative" => "-.123E-456",
			"negative_onlyDecimal_lowerSci_positive" => "-.123e456",
			"negative_onlyDecimal_lowerSci_negative" => "-.123e-456",
		);
		$expecteds = array();
		$actuals = array();
		$this->object->setExpectedPattern(TextArgumentParameter::PATTERN_NUMBER);
		foreach (array_keys($inputs) as $key) {
			$expecteds[$key] = 1;

			$actuals[$key] = $this->object->isValueValid($inputs[$key]);

		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_number_invalid() {
		$this->markTestIncomplete();
		$inputs = array(
//			"onlySci_capital_positive" => "E456",
//			"onlySci_capital_negative" => "E-456",
//			"onlySci_lower_positive" => "e456",
//			"onlySci_lower_negative" => "e-456",
			"sci_expressPositive" => "+12.3E456",
			"sci_containsLetter_preDecimal" => "a2.3E456",
			"sci_containsLetter_decimal" => "12.aE456",
			"sci_containsLetter_exponent" => "12.3Ea56",
			"sci_multiple_decimal" => "1.2.3E456",
			"sci_only_decimal" => ".E456",
			"noSci_expressPositive" => "+12.3",
			"noSci_containsLetter_preDecimal" => "a2.3",
			"noSci_containsLetter_decimal" => "12.a",
			"noSci_multiple_decimal" => "1.2.3",
			"noSci_only_decimal" => ".",
		);
		$expecteds = array();
		$actuals = array();
		$this->object->setExpectedPattern(TextArgumentParameter::PATTERN_NUMBER);
		foreach (array_keys($inputs) as $key) {
			$expecteds[$key] = 0;

			$actuals[$key] = $this->object->isValueValid($inputs[$key]);

		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_proportion_valid() {
		$inputs = array(
			"zero" => "0",
			"zero_verbose" => "0.0000000",
			"one" => "1",
			"one_verbose" => "1.000000",
			"with_zero" => "0.12",
			"without_zero" => ".12",
		);
		$expecteds = array();
		$actuals = array();
		$this->object->setExpectedPattern(TextArgumentParameter::PATTERN_PROPORTION);
		foreach (array_keys($inputs) as $key) {
			$expecteds[$key] = 1;

			$actuals[$key] = $this->object->isValueValid($inputs[$key]);

		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_proportion_invalid() {
		$inputs = array(
			"negative_one" => "-1.00000",
			"negative_zero" => "-0.00000",
			"negative" => "-.12",
			"white_space_pre" => " 0.00",
			"white_space_post" => "0.00 ",
			"white_space_middle" => "0. 00",
			"too_large" => "1.0000000000001",
			"much_too_large" => "2.000",
		);
		$expecteds = array();
		$actuals = array();
		$this->object->setExpectedPattern(TextArgumentParameter::PATTERN_PROPORTION);
		foreach (array_keys($inputs) as $key) {
			$expecteds[$key] = 0;

			$actuals[$key] = $this->object->isValueValid($inputs[$key]);

		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_noWhiteSpace_valid() {
		$inputs = array(
			"number" => "-1.23E456",
			"digit" => "1",
			"proportion" => ".99",
			"characters" => "asdf",
			"punctuation" => "!@#$,.<>/?",
		);
		$expecteds = array();
		$actuals = array();
		$this->object->setExpectedPattern(TextArgumentParameter::PATTERN_NO_WHITE_SPACE);
		foreach (array_keys($inputs) as $key) {
			$expecteds[$key] = 1;

			$actuals[$key] = $this->object->isValueValid($inputs[$key]);

		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_noWhiteSpace_invalid() {
		$inputs = array(
			"space" => "-1.23E456 ",
			"tab" => "1	",
			"newline" => "!@#$,.<>/?",
			"form_feel" => ".99",
			"carriage_return" => "asdf\r",
		);
		$expecteds = array();
		$actuals = array();
		$this->object->setExpectedPattern(TextArgumentParameter::PATTERN_NO_WHITE_SPACE);
		foreach (array_keys($inputs) as $key) {
			$expecteds[$key] = 0;

			$actuals[$key] = $this->object->isValueValid($inputs[$key]);

		}
		$this->assertEquals($expecteds, $actuals);
	}
	/**
	 * @covers TextArgumentParameter::isValueValid
	 */
	public function testIsValueValid_anythingGoes() {
		$inputs = array(
			"space" => "-1.23E456 ",
			"tab" => "1	",
			"newline" => "!@#$,.<>/?",
			"form_feel" => ".99",
			"carriage_return" => "asdf\r",
			"negative" => -1,
			"letter_precedes" => "a23",
			"letter_follows" => "23a",
			"white_space" => "9999 99999",
			"negative_one" => "-1.00000",
			"negative_zero" => "-0.00000",
			"negative" => "-.12",
			"white_space_pre" => " 0.00",
			"white_space_post" => "0.00 ",
			"white_space_middle" => "0. 00",
			"too_large" => "1.0000000000001",
			"much_too_large" => "2.000",
			"onlySci_capital_positive" => "E456",
			"onlySci_capital_negative" => "E-456",
			"onlySci_lower_positive" => "e456",
			"onlySci_lower_negative" => "e-456",
			"expressPositive" => "+12.3E456",
			"containsLetter_preDecimal" => "a2.3E456",
			"containsLetter_decimal" => "12.aE456",
			"containsLetter_exponent" => "12.3Ea56",
		);
		$expecteds = array();
		$actuals = array();
		$this->object->setExpectedPattern(TextArgumentParameter::PATTERN_ANYTHING_GOES);
		foreach (array_keys($inputs) as $key) {
			$expecteds[$key] = 1;

			$actuals[$key] = $this->object->isValueValid($inputs[$key]);

		}
		$this->assertEquals($expecteds, $actuals);
	}
}
