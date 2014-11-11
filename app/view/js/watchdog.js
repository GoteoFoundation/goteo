function keep_alive() {
	//llamar al identificador de sesion
	$.getJSON('/json/keep_alive',function(data){
		if(data.logged) {
			//do nothing...
			// console.log(data.userid);
			setTimeout(keep_alive,30000);
		}
		if(data.info) {
			//session expired
			if(data.expires <= 0) {
				// alert(data.info);
				// location.reload();
			}
			else
				if(confirm(data.info)) {
					location.reload();
				}
		}
	});

}

$(document).ready(function() {
	keep_alive();
});
