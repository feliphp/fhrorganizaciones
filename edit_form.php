<?php
/*
@Autor: JFHR
@fecha:  2015-10-29
@Comentario:  Formulario de Edición 
*/
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/lib/formslib.php');

class fhrorganizaciones_edit_form extends moodleform {

    /**
     * Define the fhr Orfanizaciones edit form
     * Formulario utilizando el Framework o API de Moodle 
     */
    public function definition() {
    	global $DB;
        $mform = $this->_form;
        $fhrorganizaciones = $this->_customdata['data'];

        $mform->addElement('text', 'name', get_string('nameorg', 'fhrorganizaciones'), 'maxlength="254" size="50"');
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        
        $options=$DB->get_records_sql("SELECT id,name,path FROM {fhr_organizaciones}");
        
        $mform->addElement('html', '<div id="fitem_id_name" class="fitem required fitem_ftext ">');
        $mform->addElement('html', '<div class="fitemtitle">');
        $mform->addElement('html', '<label name="parent" for="parent_id">Parent:</label>');
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '<span id="transmark">&nbsp;&nbsp;&nbsp;&nbsp;</spam>');
        $mform->addElement('html', ' <select name="parent_id" id="id_parent_id" >');
        $mform->addElement('html','<option value="0">PADRE</option>');
        foreach($options as $org) {
        	$ids_padres = "0".str_replace("/", ",", $org->path); // reemplaza / por coma en string
        	$ais=explode(',',$ids_padres); // genera el array
        	$da_d="";
        	$opDa="";
        	foreach($ais as $pid) {
        		$da_data = array();
        		$da_data = $DB->get_records_sql('SELECT name FROM mdl_fhr_organizaciones WHERE id = "'.$pid.'"');
	        		foreach($da_data as $da){
	        		$opDa.=$da->name."|";
	        		
	        		}
	        		//$mform->addElement('html','<option value="'.$org->id.'">'.$opDa.'</option>');	        		
        	}
        	
        	$mform->addElement('html','<option value="'.$org->id.'">'.$opDa.'</option>');
        }
        $mform->addElement('html', '</select>'."\n");




        
        
        
//         $table = 'fhr_organizaciones'; ///name of table
//         $sort = 'id'; //field or fields you want to sort the result by
//         $fields = 'id, tacos'; ///list of fields to return
        
        
//          $options = $DB->get_records_sql('SELECT id,name FROM mdl_fhr_organizaciones');
//          $mform->addElement('select', 'parent_id', get_string('parentorg', 'fhrorganizaciones'), $options);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        
        
//         $options = $this->get_category_options($cohort->contextid);
//         $mform->addElement('select', 'contextid', get_string('context', 'role'), $options);

//         $mform->addElement('text', 'idnumber', get_string('idnumber', 'cohort'), 'maxlength="254" size="50"');
//         $mform->setType('idnumber', PARAM_RAW); // Idnumbers are plain text, must not be changed.

//         $mform->addElement('advcheckbox', 'visible', get_string('visible', 'cohort'));
//         $mform->setDefault('visible', 1);
//         $mform->addHelpButton('visible', 'visible', 'cohort');

//         $mform->addElement('editor', 'description_editor', get_string('description', 'cohort'), null, $editoroptions);
//         $mform->setType('description_editor', PARAM_RAW);



//         if (isset($this->_customdata['returnurl'])) {
//             $mform->addElement('hidden', 'returnurl', $this->_customdata['returnurl']->out_as_local_url());
//             $mform->setType('returnurl', PARAM_LOCALURL);
//         }

        $this->add_action_buttons();

        $this->set_data($fhrorganizaciones);
    }

    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        return $errors;
    }

//     protected function get_category_options($currentcontextid) {
//         global $CFG;
//         require_once($CFG->libdir. '/coursecatlib.php');
//         $displaylist = coursecat::make_categories_list('moodle/cohort:manage');
//         $options = array();
//         $syscontext = context_system::instance();
//         if (has_capability('moodle/cohort:manage', $syscontext)) {
//             $options[$syscontext->id] = $syscontext->get_context_name();
//         }
//         foreach ($displaylist as $cid=>$name) {
//             $context = context_coursecat::instance($cid);
//             $options[$context->id] = $name;
//         }
//         // Always add current - this is not likely, but if the logic gets changed it might be a problem.
//         if (!isset($options[$currentcontextid])) {
//             $context = context::instance_by_id($currentcontextid, MUST_EXIST);
//             $options[$context->id] = $syscontext->get_context_name();
//         }
//         return $options;
//     }
}

