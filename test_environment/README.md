Unit Tests
==========
Copyright (C) 2014 Aaron Sharp
Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007

There are a rather large number of unit tests.  Here are some heuristics to make managing them easier:

* There is about one TestClass for each Class in includes/
* There is at least one function in the TestClass for each function in the Class
* TestClass function names are fairly verbose:

		testNameOfClassFunction_objectState_functionParameter_expectedResult() { ... }

* Each function in these tests classes follows a similar format:

		testNameOfClassFunction_objectState_functionParameter_expectedResult() {
			set expected value(s)
			set auxiliary variables
			create mock objects for dependencies
			set mock object expecations
			set environment (global variables, external files)
			set state for object under test

			call function under test

			perform any cleanup
			gather actual value(s)
			check that expected == actual
		}

* Most test cases are intentionally white-box, intended to achieve branch coverage (as well as condition coverage in some cases)
* The most problematic unit tests are those in the folder ./Models/Scripts/QIIME.  My primary goal with them was to check that certain parameter combinations were legal and others were illegal
