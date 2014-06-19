function makeTrigger(trg) {
	trg['_lstnrs'] = [];
	trg.change(function() {
		var val = $(this).val();
		if('radio' == trg.attr('type') && !($(this).is(':checked'))) {
			return;
		}
		else if (!val) {
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
}
function unRequireParam(param) {
	param.parents('label').css('color', '#330000').css('font-weight', 'normal');
}
function unExcludeParam(param) {
	param.prop('disabled', false).change().parents('label').css('display', 'block');
}
function excludeParam(param) {
	param.prop('disabled', true).change().parents('label').css('display', 'none');
}
function makeDependent(depdnt) {
	depdnt['_pot_reqs'] = {};
	depdnt['_cur_reqs'] = [];
	depdnt['_pot_allws'] = {};
	depdnt['_cur_allws'] = [];
	depdnt['_pot_exclds'] = {};
	depdnt['_cur_exclds'] = [];
	depdnt['respondTo'] = function(trg, val) {
		var trgName = trg.attr('name');
		var valAsBool = Boolean(val);
		var reqVals = depdnt['_pot_reqs'][trgName];
		if (reqVals) {
			var curReqs = depdnt['_cur_reqs'];
			var isReqd = false;
			for (var i = 0;i<reqVals.length;i++) {
				if (reqVals[i] == val || reqVals[i] == valAsBool) {
					isReqd = true;
					break;
				}
			}
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
		var curAllws = depdnt['_cur_allws'];
		if (allwVals) {
			var isAllwd = false;
			for (var i = 0;i<allwVals.length;i++) {
				if (allwVals[i] == val || allwVals[i] == valAsBool) {
					isAllwd = true;
					break;
				}
			}
			if (isAllwd) {
				curAllws.push(trgName);
			}
			else {
				for (var i = curAllws.length - 1; i >= 0; i--) {
					if (curAllws[i] == trgName) {
						curAllws.splice(i, 1);
					}
				}
			}
		}
		var excldVals = depdnt['_pot_exclds'][trgName];
		var curExclds = depdnt['_cur_exclds'];
		if (excldVals) {
			var isExcld = false;
			for (var i = 0; i<excldVals.length;i++) {
				if (excldVals[i] == val || excldVals[i] == valAsBool) {
					isExcld = true;
					break;
				}
			}
			if (isExcld) {
				curExclds.push(trgName);
				excludeParam(depdnt);
			}
			else {
				for(var i = curExclds.length - 1; i >= 0; i--) {
					if (curExclds[i] == trgName) {
						curExclds.splice(i,1);
					}
				}
			}
		}

		if (curExclds.length == 0 &&
				(jQuery.isEmptyObject(depdnt['_pot_allws']) || curAllws.length != 0)) {
			unExcludeParam(depdnt);
		}
		else {
			excludeParam(depdnt);
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
		var allwrs = depdnt['_pot_allws'];
		if (allwrs[trgName]) {
			allwrs[trgName].push(val);
		}
		else {
			allwrs[trgName] = [val];
		}
	}
	depdnt['excludeOn'] = function(trgName, val) {
		var exclds = depdnt['_pot_exclds'];
		if (exclds[trgName]) {
			exclds[trgName].push(val);
		}
		else {
			exclds[trgName] = [val];
		}
	}
	depdnt['listenTo'] = function(trg) {
		trg['_lstnrs'].push(depdnt);
	}
}
function makeEitherOr(jQueryObj) {
	jQueryObj.each(function() {
		var contentLabel = $(this).next();
		contentLabel.find('[type="checkbox"]').prop('checked', true);
	});
	jQueryObj.change(function() {
		jQueryObj.each(function() {
			var contentLabel = $(this).next();
			contentLabel.find('[name]').prop('disabled', !($(this).is(':checked')))
		})});
}
