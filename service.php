<?php

	//codigo para el ingreso de nuevos finalista a la base de datos
	if($_POST){	
		if ($_POST['action'] == 'addRunner') {
			//Filtros para cuidar el codigo de posibles amenazas con los datos de entrada
			$fname = htmlspecialchars($_POST['txtFirstName']);
			$lname = htmlspecialchars($_POST['txtLastName']);
			$gender = htmlspecialchars($_POST['ddlGender']);
			$minutes = htmlspecialchars($_POST['txtMinutes']);
			$seconds = htmlspecialchars($_POST['txtSeconds']);

			//Validación de los datos ingresados por el usuario
			if(preg_match('/[^\w\s]/i', $fname) || preg_match('/[^\w\s]/i', $lname)) {
				fail('Invalid name provided.');
			}

			if( empty($fname) || empty($lname) ) {  
				fail('Please enter a first and last name.');
			}
			if( empty($gender) ) {
				fail('Please select a gender.');
			}
			if( empty($minutes) || empty($seconds) ) {
				fail('Please enter minutes and seconds.');
			}
			
			$time = $minutes.":".$seconds;

			//sentensia para ingresar un nuevo finalista a la base de datos
			$query = "INSERT INTO runners SET first_name='$fname', last_name='$lname', gender='$gender', finish_time='$time'";
			$result = db_connection($query);
			
			//Validación del exito o falla del ingreso de los datos
			if ($result) {
				$msg = "Runner: ".$fname." ".$lname." added successfully" ;
				success($msg);
			} else {
				fail('Insert failed.');
			}
			exit;
		}
	}

	//Extraccion de la base de datos y se ordenan acendentemente según finish_time.
	$query = "SELECT first_name, last_name, gender, finish_time FROM runners order by finish_time ASC ";
	$result = db_connection($query);  //Llamada a la función que establece la coneccion con la base de datos.
	$runners = array(); //Arreglo para guardar los datos obtenidos de la base de datos. 
	while ($row = mysqli_fetch_assoc($result)) { //Ciclo de repetición para guardar los datos en el arreglo runners
		array_push($runners, array('fname' => $row['first_name'], 'lname' => $row['last_name'], 
		'gender' => $row['gender'], 'time' => $row['finish_time']));
	}

	//codificación de los datos en formato JSO
	echo json_encode(array("runners" => $runners)); 
	exit;		

	//Funcion para realizar la coneccion entre php y la base de datos
    function db_connection($query) {
    	$con = mysqli_connect('**************','***********','**********','**********') 
    	OR die( 'Could not connect to database.');
    	//Retorna los resultados de la solicitud SELECT de la base de datos.
    	return mysqli_query($con, $query);
    }
	
	//Función para reportar errores en la conversion de la matriz de datos
	function fail($message) {
		die(json_encode(array('status' => 'fail', 'message' => $message)));
	}

	//Función para reportar el exito de la conversion de la matriz de datos
	function success($message) {
		die(json_encode(array('status' => 'success', 'message' => $message)));
	}
?>