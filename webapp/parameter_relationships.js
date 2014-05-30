function requireParam(jQueryObj) {
	jQueryObj.parents('label').css('color', '#cc0000').css('font-weight', 'bold');
}

function makeAllowingRelationship(dependent, trigger, value) {
	if (!trigger['allowed']) {
		trigger['allowed'] = {};
	}
	if (value) {
		if (!trigger['allowed'][value]) {
			trigger['allowed'][value] = [];
		}
		trigger['allowed'][value].push(dependent);
	}
	else {
		if (!trigger['allow_on_any']) {
			trigger['allow_on_any'] = [];
		}
		trigger['allow_on_any'].push(dependent);
	}
	trigger.change();
}

function makeRequiringRelationship(dependent, trigger, value) {
	if (!trigger['required']) {
		trigger['required'] = {};
	}
	if (value) {
		if (!trigger['required'][value]) {
			trigger['required'][value] = [];
		}
		trigger['required'][value].push(dependent);
	}
	else {
		if (!trigger['require_on_any']) {
			trigger['require_on_any'] = [];
		}
		trigger['require_on_any'].push(dependent);
	}
	trigger.change();
}

function changeTrigger(jQueryObj) {
	var value = jQueryObj.val();
	if (value) {
		if (jQueryObj['allowed']) {
			var elemsToAllow = null;
			jQuery.each(jQueryObj['allowed'], function (index, Element) {
				if (index  == value) {
					elemsToAllow = Element;
				}
				else {
					jQuery.each(Element, function (arrayIndex, arrayElement) {arrayElement.prop('disabled', true).parents('label').css('display', 'none').change()});	
				}
			});
			if (elemsToAllow) {
				jQuery.each(elemsToAllow, function (arrayIndex, arrayElement) {arrayElement.prop('disabled', false).parents('label').css('display', 'block').change()});	
			}
		}

		if (jQueryObj['required']) {
			var elemsToRequire = null;
			jQuery.each(jQueryObj['required'], function (index, Element) {
				if (index  == value) {
					elemsToRequire = Element;
				}
				else {
					jQuery.each(Element, function (arrayIndex, arrayElement) {arrayElement.parents('label').css('color', '#330000').css('font-weight', 'normal').change()});	
				}
			});
			if (elemsToRequire) {
				jQuery.each(elemsToRequire, function (arrayIndex, arrayElement) {requireParam(arrayElement);arrayElement.change();});	
			}
		}

		if (jQueryObj['allow_on_any']) {
			jQuery.each(jQueryObj['allow_on_any'], function (index, Element) {
				Element.prop('disabled', false).parents('label').css('display', 'block').change();	
			});
		}
		if (jQueryObj['require_on_any']) {
			jQuery.each(jQueryObj['require_on_any'], function (index, Element) {
				requireParam(Element);
				Element.change();
			});
		}
	}
	else {
		if (jQueryObj['allowed']) {
			jQuery.each(jQueryObj['allowed'], function (index, Element) {
				jQuery.each(Element, function (arrayIndex, arrayElement) {arrayElement.prop('disabled', true).parents('label').css('display', 'none').change()});	
			});
		}
		if (jQueryObj['allow_on_any']) {
			jQuery.each(jQueryObj['allow_on_any'], function (index, Element) {
				Element.prop('disabled', true).parents('label').css('display', 'none').change();	
			});
		}
		if (jQueryObj['required']) {
			jQuery.each(jQueryObj['required'], function (index, Element) {
				jQuery.each(Element, function (arrayIndex, arrayElement) {arrayElement.parents('label').css('color', '#330000').css('font-weight', 'normal').change()});	
			});
		}
		if (jQueryObj['require_on_any']) {
			jQuery.each(jQueryObj['require_on_any'], function (index, Element) {
				Element.css('color', '#330000').css('font-weight', 'normal').change();	
			});
		}
	}
}
