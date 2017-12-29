<?php
/*
@Autor: JFHR
@fecha:  2015-10-28
@Comentario: ***
* Importante y requerido para instalar el módulo
*/

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete fhrorganizaciones structure for backup, with file and id annotations
 *
 * @package   mod_fhrorganizaciones
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_fhrorganizaciones_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the fhrorganizaciones instance.
        $fhrorganizaciones = new backup_nested_element('fhrorganizaciones', array('id'), array(
            'name', 'intro', 'introformat', 'grade'));

        // If we had more elements, we would build the tree here.

        // Define data sources.
        $fhrorganizaciones->set_source_table('fhrorganizaciones', array('id' => backup::VAR_ACTIVITYID));

        // If we were referring to other tables, we would annotate the relation
        // with the element's annotate_ids() method.

        // Define file annotations (we do not use itemid in this example).
        $fhrorganizaciones->annotate_files('mod_fhrorganizaciones', 'intro', null);

        // Return the root element (fhrorganizaciones), wrapped into standard activity structure.
        return $this->prepare_activity_structure($fhrorganizaciones);
    }
}
