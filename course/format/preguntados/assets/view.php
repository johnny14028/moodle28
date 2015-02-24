<?php
/* 
*../view.php?action=getInit&format=json
0. Validacion, Token, idCurso, idUsuario, idGrupo, retos

CARGAR CATEGORIAS
* ../view.php?action=getAllCategory&idCourse=2&key=101010
1. 2 datos (id, categorias), <8 repites >8 random

CARGAR PUESTOS Y GRUPOS
* ../view.php?action=getUsers&idGroup=3&key=101010
2. lista de alumnos del grupo con puesto y puesto del grupo

CARGAR PREGUNTA (random de cat y esperar mientras gira)
* ../view.php?action=getCategory&idCategory=1&key=101010
3. nombre categoria, una pregunta, alernativa y rpta correcta

ENVIAR RESPUESTA POR PREGUNTA
* ../view.php?action=SetScore&data=idPregunta_idUser_idGroup_ok&key=101010 (data=4_231_5_0)

ENVIAR RESPUESTA POR PREGUNTA (SUMAR PUNTOS)
* ../view.php?action=SetPlus&data=idPregunta_idUser_idGroup_ok&key=101010 (data=4_231_5_0)

ENVIAR RESPUESTA POR PREGUNTA (QUITAR PUNTOS)
* ../view.php?action=SetMinus&data=idPregunta_idUser_idGroup_idGroupM_ok&key=101010 (data=4_231_5_0)

*/
	$output = null;
	switch($_GET['action']){
		case "getInit":
			$output = array(
				'id'=>1,
				'validation'=>true,
				'token'=>101010,
				'idCurso'=>10,
				'idUsuario'=>4,
				'name_data'=>"User2",
				'idGrupo'=>2,
				'ranking'=>2
			);
		break;
		case "getAllCategory":
			if(isset($_GET['idCourse']) && isset($_GET['key'])){
				if($_GET['key'] == "101010"){
					$output = array(
						array(
							'id'=>1,
							'name'=>'Operaciones'
						),
						array('id'=>2,
							'name'=>'Marketing'
						),
						array('id'=>3,
							'name'=>'Cobranza'
						),
						array('id'=>4,
							'name'=>'Servicios'
						),
						array('id'=>5,
							'name'=>'Calidad'
						),
						array('id'=>6,
							'name'=>'Productos'
						),
						array('id'=>7,
							'name'=>'Ventas'
						),
						array('id'=>8,
							'name'=>'Finanzas'
						)
					);
				}
			}
		break;
		case "getUsers":
			$users = array(
				array(
					'id'=>1,
					'name_data'=>'User1',
					'score'=>2,
					'group'=>1
					),
				array(
					'id'=>2,
					'name_data'=>'User2',
					'score'=>4,
					'group'=>1
					),
				array(
					'id'=>3,
					'name_data'=>'User3',
					'score'=>4,
					'group'=>1
					),
				/*array(
					'id'=>4,
					'name_data'=>'User4',
					'score'=>7,
					'group'=>2
					),
				array(
					'id'=>5,
					'name_data'=>'User5',
					'score'=>7,
					'group'=>2
					),
				array(
					'id'=>6,
					'name_data'=>'User6',
					'score'=>5,
					'group'=>2
					),
				array(
					'id'=>7,
					'name_data'=>'User7',
					'score'=>9,
					'group'=>3
					),
				array(
					'id'=>8,
					'name_data'=>'User8',
					'score'=>1,
					'group'=>3
					),
				array(
					'id'=>9,
					'name_data'=>'User9',
					'score'=>3,
					'group'=>3
					),
				array(
					'id'=>10,
					'name_data'=>'User10',
					'score'=>7,
					'group'=>2
					),*/
				array(
					'id'=>11,
					'name_data'=>'User11',
					'score'=>5,
					'group'=>1
					)			
			);

			$groups = array(
				array(
					'id'=>1,
					'group_data'=>'Group2',
					'score'=>26
					),
				array(
					'id'=>2,
					'group_data'=>'Group1',
					'score'=>15
					),
				array(
					'id'=>3,
					'group_data'=>'Group3',
					'score'=>13
					)			
			);

			$output = array(
				'users'=>$users,
				'groups'=>$groups
			);
		break;
		case "getCategory":
			$output = array(
				'id'=>1,
				'question'=>'Pregunta 1?',
				'answers'=>array(
					'A'=>'Option 1',
					'B'=>'Option 2',
					'C'=>'Option 3',
					'D'=>'Option 4'
					),
				'correct'=>'Option 2'
			);
		break;
		case "SetScore":
			//idPregunta_idUser_idGroup_ok
			$data = explode("_", $_GET['data']);
			if(isset($data[3])){
				$output = "OK";
			}
		break;
		case "SetPlus":
			//idPregunta_idUser_idGroup_ok
			$data = explode("_", $_GET['data']);
			if(isset($data[3])){
				$output = "OK";
			}
		break;
		case "SetMinus":
			//idPregunta_idUser_idGroup_idGroupM_ok
			$data = explode("_", $_GET['data']);
			if(isset($data[4])){
				$output = "OK";
			}
		break;

	}

	echo json_encode($output)

?>