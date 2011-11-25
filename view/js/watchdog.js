function keep_alive() {
	//llamar al identificador de sesion
	$.getJSON('/json/keep_alive',function(data){
		if(data.logged) {
			//do nothing...
			//alert(data.userid);
		}
		setTimeout(keep_alive,350000);
	});

}

$(document).ready(function() {
	keep_alive();
});
