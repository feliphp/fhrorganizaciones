<?php
/*
@Autor: JFHR
@fecha:  2015-10-28
@update:  2015-11-6
@Comentario:  Funciones principales del módulo
*/

defined('MOODLE_INTERNAL') || die();

/**
 * Example constant, you probably want to remove this :-)
 */
define('fhrorganizaciones_ULTIMATE_ANSWER', 42);

// Modelo de la función para Agregar Organizaciones

function fhrorganizaciones_add_fhrorganizaciones($fhrorganizaciones) {
	global $DB;

	if (!isset($fhrorganizaciones->name)) {
		throw new coding_exception('Missing fhrorganizaciones name in fhrorganizaciones_add_fhrorganizaciones().');
	}
	if (!isset($fhrorganizaciones->parent_id)) {
		$fhrorganizaciones->parent_id = 0;
	}
	if (!isset($fhrorganizaciones->depth)) {
		$fhrorganizaciones->depth = 1;
	}
	if (!isset($fhrorganizaciones->path)) {
		$fhrorganizaciones->path = '/1';
	}

	if (!isset($fhrorganizaciones->timecreated)) {
		$fhrorganizaciones->timecreated = time();
	}
	if (!isset($fhrorganizaciones->timemodified)) {
		$fhrorganizaciones->timemodified = $fhrorganizaciones->timecreated;
	}
	if (!isset($fhrorganizaciones->grade)) {
		$fhrorganizaciones->grade = 100;
	}

	$fhrorganizaciones->id = $DB->insert_record('fhr_organizaciones', $fhrorganizaciones);

	return $fhrorganizaciones->id;
}

// End función para Agregar Organizaciones


//Modelo de la función para Agregar Usuario a  Organizacion
function agregar_a_da($da,$user){
	global $DB;

	$record = new stdClass();
	$record->id = null;
	$record->userid = $user;
	$record->fieldid = 1;
	$record->data   = $da;
	$record->dataformat = 0;
	$lastinsertid = $DB->insert_record('user_info_data', $record, false);

	return $lastinsertid;
}

//Fin Modelo de la función para Agregar Usuario a  Organizacion

//Modelo de la función para Borrar Usuario de la  Organizacion
function remover_a_da($users_da){
	global $DB;
	$DB->delete_records('user_info_data', array('id'=>$users_da));
}

function update_a_da($idfon,$da,$user) {
	global $DB;
	$data = new stdClass;
	$data->id = $idfon;
	$data->data = $da;
	$DB->update_record('user_info_data', $data);
}
//Fin Modelo de la función para Borrar Usuario de la  Organizacion 

/**
 * Delete fhrorganizaciones.
 * @param  stdClass $cohort
 * @return void
 */
// Modelo de la función para Borrar Organizaciones
function fhrorganizaciones_delete_fhrorganizaciones($fhrorganizaciones) {
	global $DB;

	$path=$fhrorganizaciones->path;
	$orgAeliminar = $DB->get_records_sql("SELECT * FROM {fhr_organizaciones} where path like '$path%'");
	foreach($orgAeliminar as $buscaInfoData){
		$DB->execute("DELETE FROM {user_info_data} where data = '$buscaInfoData->name'");
	}
	//$DB->delete_records('fhr_organizaciones', array('id'=>$fhrorganizaciones->id));
	$DB->execute("DELETE FROM {fhr_organizaciones} where path like '$path%'");
	$DB->execute("ALTER TABLE {fhr_organizaciones} AUTO_INCREMENT = 1");
}
//Fin  Modelo de la función para Borrar Organizaciones

/**
 * Updates an instance of the fhrorganizaciones in the database
 *
 */

// Modelo de la función para Actualizar datos de las Organizaciones
function fhrorganizaciones_update_fhrorganizaciones($fhrorganizaciones) {
    global $DB;

    $fhrorganizaciones->timemodified = time();
    $DB->update_record('fhr_organizaciones', $fhrorganizaciones);
}
//Fin  Modelo de la función para Actualizar datos de las Organizaciones


/**
 * Removes an instance of the fhrorganizaciones from the database
 */

// Función para borrar organizaciones
function fhrorganizaciones_delete_instance($id) {
    global $DB;

    if (! $fhrorganizaciones = $DB->get_record('fhrorganizaciones', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.

    $DB->delete_records('fhrorganizaciones', array('id' => $fhrorganizaciones->id));

    fhrorganizaciones_grade_item_delete($fhrorganizaciones);

    return true;
}
// Fin Función para borrar organizaciones


/////////////////// MAS FUNCIONES QUE CONTENIA EL MODULO COHORTS
/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $fhrorganizaciones The fhrorganizaciones instance record
 * @return stdClass|null
 */
function fhrorganizaciones_user_outline($course, $user, $mod, $fhrorganizaciones) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $fhrorganizaciones the module instance record
 */
function fhrorganizaciones_user_complete($course, $user, $mod, $fhrorganizaciones) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in fhrorganizaciones activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function fhrorganizaciones_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link fhrorganizaciones_print_recent_mod_activity()}.
 */
function fhrorganizaciones_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@link fhrorganizaciones_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function fhrorganizaciones_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function fhrorganizaciones_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function fhrorganizaciones_get_extra_capabilities() {
    return array();
}

/* Gradebook API */

/**
 * Is a given scale used by the instance of fhrorganizaciones?
 *
 * This function returns if a scale is being used by one fhrorganizaciones
 * if it has support for grading and scales.
 *
 * @param int $fhrorganizacionesid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given fhrorganizaciones instance
 */
function fhrorganizaciones_scale_used($fhrorganizacionesid, $scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('fhrorganizaciones', array('id' => $fhrorganizacionesid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of fhrorganizaciones.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any fhrorganizaciones instance
 */
function fhrorganizaciones_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('fhrorganizaciones', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given fhrorganizaciones instance
 *
 */
function fhrorganizaciones_grade_item_update(stdClass $fhrorganizaciones, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($fhrorganizaciones->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($fhrorganizaciones->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $fhrorganizaciones->grade;
        $item['grademin']  = 0;
    } else if ($fhrorganizaciones->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$fhrorganizaciones->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('mod/fhrorganizaciones', $fhrorganizaciones->course, 'mod', 'fhrorganizaciones',
            $fhrorganizaciones->id, 0, null, $item);
}

/**
 * Delete grade item for given fhrorganizaciones instance
 *
 * @param stdClass $fhrorganizaciones instance object
 * @return grade_item
 */
function fhrorganizaciones_grade_item_delete($fhrorganizaciones) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/fhrorganizaciones', $fhrorganizaciones->course, 'mod', 'fhrorganizaciones',
            $fhrorganizaciones->id, 0, null, array('deleted' => 1));
}

/**
 * Update fhrorganizaciones grades in the gradebook
 */
function fhrorganizaciones_update_grades(stdClass $fhrorganizaciones, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = array();

    grade_update('mod/fhrorganizaciones', $fhrorganizaciones->course, 'mod', 'fhrorganizaciones', $fhrorganizaciones->id, 0, $grades);
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 */
function fhrorganizaciones_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for fhrorganizaciones file areas
 *
 */
