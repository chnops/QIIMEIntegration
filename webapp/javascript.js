function hideMe(me) {
	me.toggle('blind', {}, 500);
}	
var hideableFields = [];
var displayedHideableId = "";
function displayHideables(hideableToDisplayId) {
	for (var i = 0; i < hideableFields.length; i++) {
		var hideableToDisplay = document.getElementById(hideableFields[i] + "_" + hideableToDisplayId);
		var displayedHideable = document.getElementById(hideableFields[i] + "_" + displayedHideableId);
		if (displayedHideable) displayedHideable.style.display="none";
		if (hideableToDisplay) hideableToDisplay.style.display="block";
	}
	displayedHideableId = hideableToDisplayId;
}
$(function() {
	var accordionContainer = $('.accordion').parent();
	accordionContainer.width(accordionContainer.width());
})
