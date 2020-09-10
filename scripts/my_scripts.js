$(document).ready(function () {

	var FREQ = 10000;
	var repeat = true;

	showFrequency();
	getDBRacers();
	startAJAXcalls();

	function showFrequency() {
		$("#freq").html("Page refreshes every " + FREQ / 1000 + " second(s).");
	}

	function startAJAXcalls() {
		if (repeat) {
			setTimeout(function () {
				getDBRacers();
				startAJAXcalls();
			},
				FREQ
			);
		}
	}

	//Función para obtener la informacion del archivo service.php
	function getDBRacers() {
		$.getJSON("service.php?action=getRunners", function (json) {
			if (json.runners.length > 0) { // Verificación para saber si hay datos en el arreglo runners
				$('#finishers_m').empty();
				$('#finishers_f').empty();
				$('#finishers_all').empty();

				$.each(json.runners, function () {
					var info = '<li>Name: ' + this['fname'] + ' ' + this['lname'] + '. Time: ' + this['time'] + '</li>';
					//Verificación del genero del corredor
					if (this['gender'] == 'm') {
						$('#finishers_m').append(info);
					} else if (this['gender'] == 'f') {
						$('#finishers_f').append(info);
					} else { }
					$('#finishers_all').append(info);
				});
			}
		});
		getTimeAjax();
	}

	//Función para enviar los datos al servidor

	$('#btnSave').click(function () {

		var data = $("#addRunner :input").serializeArray();

		$.post($("#addRunner").attr('action'), data, function (json) {

			if (json.status == "fail") {
				alert(json.message);
			}
			if (json.status == "success") {
				alert(json.message);
				clearInputs();
			}
		}, "json");

	});

	//Función para limpiar los campos del formulario una vez se envien los datos
	function clearInputs() {
		$("#addRunner :input").each(function () {
			$(this).val('');
		});;
	}

	//Función para el boton de envio de los datos 
	$("#addRunner").submit(function () {
		return false;
	});

	function getTimeAjax() {
		var time = "";
		$.ajax({
			url: "time.php",
			cache: false,
			success: function (data) {
				$('#updatedTime').html(data);
			}
		});
	}

	$("#btnStop").click(function () {
		repeat = false;
		$("#freq").html("Updates paused.");
	});

	$("#btnStart").click(function () {
		repeat = true;
		startAJAXcalls();
		showFrequency();
	});

});
