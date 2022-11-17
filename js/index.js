// JavaScript Document

function ingresar() {
	//Se valida que se hayan ingresado el usuario y la contraseña
	if (trim(document.getElementById("txt_usuario").value).length <= 4) {
		alert("Debe ingresar un nombre de usuario v\xe1lido.");
		document.getElementById("txt_usuario").focus();
		return;
	}
	if (trim(document.getElementById("txt_contrasena").value).length <= 4) {
		alert("Debe ingresar una contrase\xf1a v\xe1lida.");
		document.getElementById("txt_contrasena").focus();
		return;
	}
	
	document.getElementById("btn_ingresar").disabled = true;
	document.getElementById("hdd_ingresar").value = 1;
	document.getElementById("frm_ingreso").submit();
}

function oprimirEnter(evento, tipo) {
	var tecla = 0;
	if (evento.keyCode) {
		tecla = evento.keyCode;
	} else if (evento.which) {
		tecla = evento.which;
	} else {
		return false;
	}
	if (tecla == 13) {
		switch (tipo) {
			case 1: //En login
				document.getElementById("txt_contrasena").focus();
				break;
			case 2: //En contraseña
				ingresar();
				break;
		}
	}
	return true;
}
