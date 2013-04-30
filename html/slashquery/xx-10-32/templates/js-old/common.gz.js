$(document).ready(function() {

	var $showDateTime = $("#showDateTime");

	function updateTheTime() {
		var currentTime = new Date()
		var month = currentTime.getMonth() + 1
		var day = currentTime.getDate()
		var year = currentTime.getFullYear()
		var hour = currentTime.getHours()
		var min = currentTime.getMinutes()
		var sec = currentTime.getSeconds()

		function pad(n) {
			return n < 10 ? '0' + n : n
		}
		$showDateTime.html(day + '&middot;' + pad(month) + '&middot;' + year + ' &nbsp;' + hour + ':' + pad(min) + ':' + pad(sec));
	}
	setInterval(updateTheTime, 1000);

});