function setCounterButtonStateStop() {
	$('#stopAddCounter, #stopPurgeCounter').show();
	$('#startCounter').hide();
}

function setCounterButtonStateStart() {
	$('#stopAddCounter, #stopPurgeCounter').hide();
	$('#startCounter, , #markCompleted').show();
}

function setButtonStateCompleted() {
	$('#stopAddCounter, #stopPurgeCounter, #startCounter, #markCompleted').hide();
	$('#markOpen').show();
}

function setButtonStateClosed() {
	$('#markClosed').hide();
	$('#markOpen').show();
}

function setButtonStateOpen() {
	$('#markClosed').show();
	$('#markOpen').hide();
	setCounterButtonStateStart();
}

function setButtonStateRejectedOpen() {
	$('#markRejected').show();
}
function setButtonStateRejectedClosed() {
	$('#markRejected').hide();
}
