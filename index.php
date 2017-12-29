<?php
/*
@Autor: JFHR
@fecha:  2015-10-28
@update:  2015-11-6
@Comentario:  Se utiliza como vista principal o controlador principal del módulo
*/

// Replace organizaciones fhr with the name of your module and remove this line.
// archivos requeridos de configuración de moodle
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->libdir.'/adminlib.php');
// forma de iniciar y cachar variables en moodle, pueden recibir GET, Post, arrays, objetos,etc., necesitan valor de inicio
$contextid = optional_param('contextid', 0, PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT); // Category id
$parent = optional_param('parent', -1, PARAM_INT); // parent id
$depth = optional_param('depth', -1, PARAM_INT); // depth - profundidad
$path = optional_param('path', -1, PARAM_TEXT); // path
$da = optional_param('da', '', PARAM_TEXT); // da
$openlink = optional_param('openlink', 0, PARAM_INT); // openlink
$user = optional_param('user', 0, PARAM_INT); //
$users_da = optional_param('users_da', '', PARAM_TEXT); //
$accion = optional_param('accion', '', PARAM_TEXT); //
$page = optional_param('page', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
////////////

$strname = get_string('fhrorganizaciones', 'fhrorganizaciones');
$site = get_site();
$params = array('page' => $page);
$baseurl = new moodle_url('/mod/fhrorganizaciones/index.php', $params);
$headerTable=0;
$ultimoNivel=0;

// login necesario para restringir acceso
require_login();
/* Variables de diseño y configuración de página*/
if ($contextid) {
	$context = context_system::instance();
} else {
	$context = context_system::instance();
}

$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($site->fullname);

if ($returnurl) {
	$returnurl = new moodle_url($returnurl);
} else {
	$returnurl = new moodle_url('/mod/fhrorganizaciones/index.php');
}
// Bloque Agregar Usuarios a ORG
$accion = substr($accion, 4,-4); // intento limpiar elementos nocivos como las fechas que aparecen en el thema <-

if($accion=='Agregar Usu'){ // if que identifica si la herramienta borra o (en este caso) agrega usuarios a org
	global $DB;
	$da_null = $DB->get_records_sql("SELECT id,userid,data,fieldid FROM mdl_user_info_data WHERE fieldid = 1 AND  userid = '$user' ");

		foreach($da_null as $infonull) {
			$infon=$infonull->data;
			$idfon=$infonull->id;
		}

if(($da != $infon)&&($infon!=null)){
	$strheading = get_string('updDa', 'fhrorganizaciones');
	$PAGE->navbar->add($strheading);
	$PAGE->set_title($strheading);
	echo $OUTPUT->header();
	echo $OUTPUT->heading($strheading);
	$paramslink = array('depth' => $depth,'path' => $path,'da' => $da,'openlink' => $openlink);
	$urlda=new moodle_url('/mod/fhrorganizaciones/index.php', $paramslink);
	$yesurl = new moodle_url('/mod/fhrorganizaciones/edit.php', array('id' => $infon, 'updateda' => 1,'idfon' => $idfon,'da' => $da,'user' => $user,'depth' => $depth,'path' => $path,'da' => $da,'openlink' => $openlink,
			'confirm' => 1, 'sesskey' => sesskey(), 'returnurl' => $returnurl->out_as_local_url()));
	$message = get_string('updaconfirm','fhrorganizaciones');
	echo $OUTPUT->confirm($message, $yesurl, $urlda);
	echo $OUTPUT->footer();
	die;
		}else{
			agregar_a_da($da,$user);
		}
	//
} else if ($accion=='ver Usuari'){
	remover_a_da($users_da);
}

admin_externalpage_setup('userbulk', '', null, '', array('pagelayout'=>'report'));

$strorg = get_string('fhrorganizaciones', 'fhrorganizaciones');
echo $PAGE->set_title($strorg);  // pinta el título de la página
echo $OUTPUT->header();  // pinta el header de la página moodle
echo $OUTPUT->heading($strname);  // pinta breadcrumb moodle


// Obtener ultimo valor del Path
$pathSeparado = "0".str_replace("/", ",", $path); // reemplaza / por coma en string
$arayIds=explode(',',$pathSeparado); // genera el array
$idOrg=end($arayIds);


//Función Para pintar el Árbol de Clasificaciones
function traer($path,$posAct,$depth,$OUTPUT,$baseurl,$table, $openlink){
	global $DB;
	$dastyle="";
	$ids_padres = "0".str_replace("/", ",", $path); // reemplaza / por coma en string
	$ais=explode(',',$ids_padres); // genera el array
	$idActual=$ais[$posAct]; // obtiene el id_parent de la posision actual

	if($posAct < $depth){ // si es menor la posición que el nivel
		$idPosSig= $ais[$posAct+1]; // Busca el siguiente elemento del array y lo asigna a $idPosSig
	} else {
		$idPosSig=0;
	}

	$organizaciones = $DB->get_records('fhr_organizaciones', array('parent_id'=>$idActual)); // obtieneregistros de un solo dato

	foreach($organizaciones as $itemHijo){  // recorre el array por los registros del query del id de categoría actual  POR FILA O COLUMNA
		$daunicofor = $DB->get_record_sql('SELECT COUNT(id) FROM {fhr_organizaciones} WHERE path like "%'.$itemHijo->path.'%" ');
		$ultimoNivel=end($daunicofor);

		$urlparams = array('id' => $itemHijo->id, 'returnurl' => $baseurl->out_as_local_url());
		 		$editurl = new moodle_url('/mod/fhrorganizaciones/edit.php', $urlparams);
 		 		$editicon = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('editexternalorg', 'fhrorganizaciones'))); // datos para generar boton edicion

		 		$deletelink = new moodle_url('/mod/fhrorganizaciones/edit.php', $urlparams + array('delete' => 1));

 		 		$deleteicon = $OUTPUT->action_icon($deletelink, new pix_icon('t/delete', get_string('deleteexternalorg', 'fhrorganizaciones')));
 		 		$daunicofor = $DB->get_record_sql('SELECT COUNT(id) FROM {fhr_organizaciones} WHERE path like "%'.$itemHijo->path.'%" '); // datos para generar boton borrar

		if($itemHijo->id == $idPosSig){   // del Path identifica si ya recorrió todos los niveles y si tiene hijos
			if($ultimoNivel==1){
				$dastyle="da";
				$openlink=1;
			} else {
				$openlink=1;
				$dastyle="fa";
			}
	 			if($depth==$itemHijo->depth){   // si la profundidad obtenida es igual a la del query
	 				if($depth == 0){   // si es el primero, si abre, pasa datos al link
	 					if($openlink==0){
	 						$depthlink=$itemHijo->depth;
	 					} else{
	 						$depthlink=$itemHijo->depth-1;
	 					}
	 				} else {
	 					$depthlink=$itemHijo->depth-1;
	 				}
	 				$paramslink = array('depth' => $depthlink,'path' => $itemHijo->path,'da' => $itemHijo->name,'openlink' => $openlink);
	 			} else {
	 				if($openlink==0){
	 					$depthlink=$itemHijo->depth;
	 				} else{
	 					$depthlink=$itemHijo->depth-1;
	 				}
	 				$paramslink = array('depth' => $depthlink,'path' => $itemHijo->path,'da' => $itemHijo->name,'openlink' => $openlink);
	 			}


	 			$table->data[] = new html_table_row(array("<span class='nivel-".$itemHijo->depth."".$dastyle."'></span>"."<a href='".new moodle_url('/mod/fhrorganizaciones/index.php', $paramslink)."'>".$itemHijo->name."</a>",
	 					$editicon . '&nbsp'. $deleteicon));

				traer($path,$posAct+1,$depth,$OUTPUT,$baseurl,$table,$openlink);
		} else{
			if($ultimoNivel==1){
				$dastyle="da";
				$openlink=0;
			} else {
				$dastyle="";
				$openlink=0;
			}
			// primera vez o si no tiene más
		 	if($depth==$itemHijo->depth){
		 	 if($depth == 0){
		 	 	 		if($openlink==0){
	 						$depthlink=$itemHijo->depth;
	 					} else{
	 						$depthlink=$itemHijo->depth-1;
	 					}
 				} else {
 					$depthlink=$itemHijo->depth-1;
 				}

 				$paramslink = array('depth' => $depthlink,'path' => $itemHijo->path,'da' => $itemHijo->name,'openlink' => $openlink );
 			} else {
 				 	if($openlink==0){
	 					$depthlink=$itemHijo->depth;
	 				} else{
	 					$depthlink=$itemHijo->depth-1;
	 				}
 				$paramslink = array('depth' => $depthlink,'path' => $itemHijo->path,'da' => $itemHijo->name,'openlink' => $openlink );
 			}


			$table->data[] = new html_table_row(array("<span class='nivel-".$itemHijo->depth."".$dastyle."'></span>"."<a href='".new moodle_url('/mod/fhrorganizaciones/index.php', $paramslink)."'>".$itemHijo->name."</a>",
					//$itemHijo->parent_id,
					$editicon . '&nbsp'. $deleteicon));
		}
	}

}


//FIN Función Para pintar el Árbol de Clasificaciones

$contador = $DB->count_records('fhr_organizaciones', array('parent_id'=>'0'));

//desabilitar formulario cuando no es DA ------ Último registro//
$daunico=array();
$daunico = $DB->get_record_sql('SELECT COUNT(id) FROM {fhr_organizaciones} WHERE path like "%'.$path.'%" ');
$fdata=end($daunico);

if($fdata == 1){
	$controlVisible="";
	$dalabel=$da;
}else{
	$controlVisible="disabled";
	$dalabel="";
}
//End desabilitar formulario ------ Último registro//



if($contador == 0){
	$url = new moodle_url('/mod/fhrorganizaciones/edit.php', array('action'=>'edit'));
	echo $OUTPUT->single_button($url, get_string('addneworg', 'fhrorganizaciones'), 'get');
} else {

// Tabla Izquiera del Armol de Organizaciones
	echo '<table class="generaltable generalbox groupmanagementtable boxaligncenter">'."\n";
	echo '<tr>'."\n";
	echo "<td>\n";

	$table = new html_table();
	$table->cellpadding = 4;
	$table->attributes['class'] = 'generaltable boxaligncenter';
	if($headerTable==0){
		$table->head = array(get_string('name'),get_string('actions', 'fhrorganizaciones'));
		$headerTable=1;
	}

	if($depth == -1){
		$organizaciones = $DB->get_records('fhr_organizaciones', array('parent_id'=>'0'));
		$table->data[]=traer(0,0,0,$OUTPUT,$baseurl,$table,$openlink);
		echo html_writer::table($table);
	} else{
		$table->data[]= traer($path,0,$depth,$OUTPUT,$baseurl,$table,$openlink);
		echo html_writer::table($table);
	}



	$url = new moodle_url('/mod/fhrorganizaciones/edit.php', array('action'=>'edit'));
	echo $OUTPUT->single_button($url, get_string('addneworg', 'fhrorganizaciones'), 'get');




	echo '</td>'."\n";
	echo "<td>\n";

// Usuarios  Tabla Derecha

	echo '<form id="groupeditform" action="index.php" method="post">'."\n";
	echo '<div>'."\n";
	echo '<input type="hidden" name="id" value="' . $idOrg . '" />'."\n";
	echo '<input type="hidden" name="depth" value="' . $depth . '" />'."\n";
	echo '<input type="hidden" name="path" value="' . $path . '" />'."\n";
	echo '<input type="hidden" name="da" value="' . $da . '" />'."\n";

	echo '<table  class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">'."\n";
	echo '<tr>'."\n";


	echo "<td>\n";
	echo '<p><label for="groups"><span id="groupslabel">'.get_string('modulename', 'fhrorganizaciones').':'.$dalabel.'</span><span id="thegrouping">&nbsp;</span></label></p>'."\n";

	$onchange = 'M.core_group.membersCombo.refreshMembers();';

// usuarios en la DA u Organización
 	echo '<select '.$controlVisible.' name="users_da" multiple="multiple" id="groups" size="15" style="width:320px" class="select" onchange="'.$onchange.'  ">'."\n";
	//obtener id de campo da
		$idfieldda=Array();
		$idfieldda = $DB->get_records_sql("SELECT id FROM mdl_user_info_field WHERE shortname ='organizaciones'");
		$ida=$idfieldda[1]->id;


 	$member_da = array();
 	if($da==''){
 		$options_da = $DB->get_records_sql("SELECT a.id, a.firstname, a.lastname , b.id , b.data, b.fieldid FROM mdl_user a, mdl_user_info_data b WHERE a.id=b.userid AND b.fieldid = '".$ida."' AND b.data = null ORDER BY firstname ASC");
 	}else{
 		$options_da = $DB->get_records_sql("SELECT a.id, a.firstname, a.lastname , b.id , b.data, b.fieldid FROM mdl_user a, mdl_user_info_data b WHERE a.id=b.userid AND b.fieldid = '".$ida."' AND b.data ='".$da."' ORDER BY firstname ASC");
 	}

  	foreach($options_da as $member_da) {
  		$completename_da=$member_da->id." || ".$member_da->firstname.' '.$member_da->lastname;
  		$completename_da=substr($completename_da, 0, 32);
  		echo '<option value="'.$member_da->id.'">'.$completename_da.'</option>';
  	}


	echo '</select>'."\n";

	echo '<p><input type="submit"  name="accion" '
			. 'id="showdeletemembersform" value="' . get_string('deleteuserstogroup', 'fhrorganizaciones'). '&rarr;" /></p>'."\n";
	echo '</td>'."\n";
	echo '<td>'."\n";
	if($da==''){
		$options = $DB->get_records_sql('SELECT mud.id,mud.username,mud.firstname,mud.lastname,mui.data  FROM mdl_user mud LEFT JOIN mdl_user_info_data mui ON mud.id=mui.userid  ORDER BY mud.firstname ASC  ');
		$num=$DB->count_records_sql('SELECT COUNT(id)  FROM mdl_user ');
	} else {
		$options = $DB->get_records_sql("SELECT mud.id,mud.username,mud.firstname,mud.lastname,mui.data,mui.userid FROM mdl_user mud LEFT JOIN mdl_user_info_data mui ON mud.id=mui.userid AND NOT EXISTS(SELECT idnumber a FROM mdl_user a, mdl_user_info_data b WHERE a.idnumber=b.userid AND b.data ='".$da."')  ORDER BY mud.firstname ASC");
		$num=$DB->count_records_sql("SELECT COUNT(id) FROM mdl_user WHERE id NOT IN(SELECT a.id FROM mdl_user a, mdl_user_info_data b WHERE a.id=b.userid AND  b.fieldid = '".$ida."'   AND  b.data ='".$da."' )");
	}
	echo '<p><label for="members"><span id="memberslabel">'.$num.' '.
			get_string('membersofselectedgroup', 'group').
			' </span><span id="thegroup"></span></label></p>'."\n";
//Todos los Usuarios
			//NOTE: the SELECT was, multiple="multiple" name="user[]" - not used and breaks onclick.
			echo '<select  '.$controlVisible.' name="user" id="members" size="15" style="width:320px" class="select"'."\n";
			echo ' onclick="window.status=this.options[this.selectedIndex].title;" onmouseout="window.status=\'\';">'."\n";

			$member_names = array();

			$atleastonemember = false;


			//$options = $DB->get_records('user');

						foreach($options as $member) {
							$mi=$member->id;
// 							$da_data = $DB->get_records_sql('SELECT userid,data FROM mdl_user_info_data WHERE userid = "'.$mi.'"');
// 							$info="";
// 								foreach($da_data as $infodata) {
// 									$info=$infodata->data;
// 								}
							// Formato de Imagen Usuarios
							$completename=$member->username." || ". $member->firstname.' '.$member->lastname;
							$completename=substr($completename, 0, 32);
							$completename.=" || ".$member->data;
							if($member->data !=$da){
							echo '<option value="'.$member->id.'">'.$completename.'</option>';
							}
							$atleastonemember = true;
						}


			if (!$atleastonemember) {
				// Print an empty option to avoid the XHTML error of having an empty select element
				echo '<option>&nbsp;</option>';
			}

			echo '</select>'."\n";

 			echo '<p><input type="submit"  name="accion" '
 					. 'id="showaddmembersform" value="&larr; ' . get_string('adduserstogroup', 'fhrorganizaciones'). '" /></p>'."\n";
					echo '</td>'."\n";
					echo '</tr>'."\n";
					echo '</table>'."\n";


					echo '</div>'."\n";
					echo '</form>'."\n";
					echo '</td>'."\n";
					echo '</tr>'."\n";
					echo '</table>'."\n";


}


echo $OUTPUT->footer();
