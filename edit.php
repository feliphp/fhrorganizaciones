<?php
/*
@Autor: JFHR
@fecha:  2015-10-28 
@update: 20015-11-5
@Comentario:  Controlador de Edición e ingreso de Organizaciones
*/
require('../../config.php');
require($CFG->dirroot.'/mod/fhrorganizaciones/lib.php');
require($CFG->dirroot.'/mod/fhrorganizaciones/edit_form.php');

$id        = optional_param('id', 0, PARAM_INT);
$idfon        = optional_param('idfon', 0, PARAM_INT);
$da = optional_param('da', '', PARAM_TEXT); // da
$user = optional_param('user', '', PARAM_TEXT); //
$openlink = optional_param('openlink', 0, PARAM_INT); // openlink
$contextid = optional_param('fhrorganizaciones', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$delete    = optional_param('delete', 0, PARAM_BOOL);
$updateda    = optional_param('updateda', 0, PARAM_BOOL);
$confirm   = optional_param('confirm', 0, PARAM_BOOL);
$depth = optional_param('depth', -1, PARAM_INT); // depth - profundidad
$path = optional_param('path', -1, PARAM_TEXT); // path
$context = context_system::instance();
require_login();
$category = null;

$paramslink = array('depth' => $depth,'path' => $path,'da' => $da,'openlink' => $openlink);
$urlda=new moodle_url('/mod/fhrorganizaciones/index.php', $paramslink);


if ($returnurl) {
	$returnurl = new moodle_url($returnurl);
} else {
	$returnurl = new moodle_url('/mod/fhrorganizaciones/index.php', array('depth' => $depth,'path' => $path,'da' => $da,'openlink' => $openlink));
}
//$ultimoid = $DB->get_record_sql('SELECT MAX(id) FROM {fhr_organizaciones}');
//$cuid= current($control);
if ($updateda == 1) {
	if ($confirm) {
		update_a_da($idfon,$da,$user);
		redirect($urlda);
	}
}


if ($id) {
	$fhrorganizaciones = $DB->get_record('fhr_organizaciones', array('id'=>$id), '*', MUST_EXIST);
} else {
	$fhrorganizaciones = new stdClass();
	$fhrorganizaciones->id          = 0;
	$fhrorganizaciones->name        = '';
	$fhrorganizaciones->depth        = '';
	$fhrorganizaciones->path          = 0;
}


/* Variableds de diseño y configuración de página*/
$PAGE->set_context($context);
$baseurl = new moodle_url('/mod/fhrorganizaciones/edit.php', array('contextid' => $fhrorganizaciones->id, 'id' => $fhrorganizaciones->id));
$PAGE->set_url($baseurl);
//$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');




$strheading = get_string('fhrorganizaciones', 'fhrorganizaciones');
$PAGE->set_title($strheading);
$PAGE->navbar->add($strheading);
if ($delete and $fhrorganizaciones->id) {
	$PAGE->url->param('delete', 1);
	if ($confirm) {
		fhrorganizaciones_delete_fhrorganizaciones($fhrorganizaciones);
		redirect($returnurl);
	}
	$strheading = get_string('delcohort', 'fhrorganizaciones');
	$PAGE->navbar->add($strheading);
	$PAGE->set_title($strheading);
	echo $OUTPUT->header();
	echo $OUTPUT->heading($strheading);
	$yesurl = new moodle_url('/mod/fhrorganizaciones/edit.php', array('id' => $fhrorganizaciones->id, 'delete' => 1,
			'confirm' => 1, 'sesskey' => sesskey(), 'returnurl' => $returnurl->out_as_local_url()));
	$message = get_string('delconfirm', 'fhrorganizaciones', format_string($fhrorganizaciones->name));
	echo $OUTPUT->confirm($message, $yesurl, $returnurl);
	echo $OUTPUT->footer();
	die;
}

$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);
$PAGE->navbar->add($strheading);


 $editform = new fhrorganizaciones_edit_form(null, array(null , 'data'=>$fhrorganizaciones, 'returnurl'=>$returnurl));

 if ($editform->is_cancelled()) {
     redirect($returnurl);

 } else if ($data = $editform->get_data()) {
//crear path
 	$d1=$_POST['parent_id']; // del seleccionado
 	$data->parent_id= $d1;
 	if ($data->id) {
 		$updateorg = $DB->get_records('fhr_organizaciones', array('id'=>$d1));
 		$creapathparte1=$updateorg[$d1]->path;
 		$ultimoid =$data->id;
 		$path=$creapathparte1."/".$data->id;
 	} else {
 		$creapathparte1= $DB->get_record_sql("SELECT path FROM {fhr_organizaciones} WHERE id = '$d1'");
 		$ultimoid = $DB->get_record_sql('SELECT MAX(id) FROM {fhr_organizaciones}');
 		$creapathparte2= current($ultimoid) + 1; 
 		$path=current($creapathparte1)."/".$creapathparte2;
 	}
 	
 	
 	$data->path = $path;
 	
// crear depth
 	$creadepthparte1= $DB->get_record_sql("SELECT depth FROM {fhr_organizaciones} WHERE id = '$d1'");
 	$depth=current($creadepthparte1)+1;
 	
 	
     if ($data->id) {
         fhrorganizaciones_update_fhrorganizaciones($data);
     } else {
     	$data->depth =$depth;
         fhrorganizaciones_add_fhrorganizaciones($data);
     }

     if ($returnurl->get_param('showall')) {
         // Redirect to where we were before.
         redirect($returnurl);
     } else {
         // Use new context id, it has been changed.
         redirect(new moodle_url('/mod/fhrorganizaciones/index.php', array('contextid' => $fhrorganizaciones->id,'depth' => $depth,'path' => $path,'da' => $da,'openlink' => $openlink)));
     }
 }

echo $OUTPUT->header();
echo $OUTPUT->heading($strheading);

// if (!$id && ($editcontrols = cohort_edit_controls($context, $baseurl))) {
//     echo $OUTPUT->render($editcontrols);
// }

echo $editform->display();
echo $OUTPUT->footer();

