function makeTrigger(trg) {
	trg['_lstnrs'] = [];
	trg.change(function() {
		var val = trg.val();
		if (!val) {
			val = false;
		}
		else if (val == "on") {
			val = trg.is(':checked');
		}
		jQuery.each(trg['_lstnrs'], function(i, lstnr) {
				lstnr.respondTo(trg, val);
			});
		});
}
function requireParam(param) {
	param.parents('label').css('color', '#cc0000').css('font-weight', 'bold');
	param.change();
}
function unRequireParam(param) {
	param.parents('label').css('color', '#330000').css('font-weight', 'normal');
	param.change();
}
function unExcludeParam(param) {
	param.prop('disabled', false).change().parents('label').css('display', 'block');
	param.change();
}
function excludeParam(param) {
	param.prop('disabled', true).change().parents('label').css('display', 'none');
	param.change();
}
function makeDependent(depdnt) {
	depdnt['_pot_reqs'] = {};
	depdnt['_cur_reqs'] = [];
	depdnt['_pot_allws'] = {};
	depdnt['_cur_allws'] = [];
	depdnt['respondTo'] = function(trg, val) {
		var trgName = trg.attr('name');
		var reqVals = depdnt['_pot_reqs'][trgName];
		if (reqVals) {
			var curReqs = depdnt['_cur_reqs'];
			var isReqd = (-1 != reqVals.indexOf(val));
			if (isReqd) {
				curReqs.push(trgName);
				requireParam(depdnt);
			}
			else {
				for (var i = curReqs.length - 1; i >= 0; i--) {
					if (curReqs[i] == trgName) {
						curReqs.splice(i, 1);
					}
				}
				if (curReqs.length == 0) {
					unRequireParam(depdnt);
				}
			}
		}
		var allwVals = depdnt['_pot_allws'][trgName];
		if (!allwVals) {
			return;
		}
		var curAllws = depdnt['_cur_allws'];
		var isAllwd = (-1 != allwVals.indexOf(val));
		if (isAllwd) {
			curAllws.push(trgName);
			unExcludeParam(depdnt);
		}
		else {
			for (var i = curAllws.length - 1; i >= 0; i--) {
				if (curAllws[i] == trgName) {
					curAllws.splice(i, 1);
				}
			}
			if (curAllws == 0) {
				excludeParam(depdnt);
			}
		}
	}
	depdnt['requireOn'] = function(trgName, val) {
		var reqs = depdnt['_pot_reqs'];
		if (reqs[trgName]) {
			reqs[trgName].push(val);
		}
		else {
			reqs[trgName] = [val];
		}
	}
	depdnt['allowOn'] = function(trgName, val) {
		var allowers = depdnt['_pot_allws'];
		if (allowers[trgName]) {
			allowers[trgName].push(val);
		}
		else {
			allowers[trgName] = [val];
		}
	}
	depdnt['listenTo'] = function(trg) {
		trg['_lstnrs'].push(depdnt);
	}
}
function makeEitherOr(jQueryObj) {
	jQueryObj.change(function() {
			if ($(this).is(':checked')) {
				jQueryObj.parents('td').find('[name]').prop('disabled', true);
				jQueryObj.prop('disabled', false);
				$(this).parents('td').find('[name]').prop('disabled', false);
				}
			});
}
