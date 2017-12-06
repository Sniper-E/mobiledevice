function refreshpage(interval, countdownel, totalel){
	var countdownel = document.getElementById(countdownel)
	var totalel = document.getElementById(totalel)
	var timeleft = interval+1
	var countdowntimer

	function countdown(){
		timeleft--
		countdownel.innerHTML = timeleft
		if (timeleft == 0){
			clearTimeout(countdowntimer)
			window.location.reload()
		}
		countdowntimer = setTimeout(function(){
			countdown()
		}, 1000)
	}

	countdown()
}

window.onload = function(){
	refreshpage(10, "countdown") // refreshpage(duration_in_seconds, id_of_element_to_show_result)
}
