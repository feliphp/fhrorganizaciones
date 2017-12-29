<?php
/*
 @Autor: JFHR
 @fecha:  2015-10-28
 @Comentario: ***
 * Importante y requerido para instalar el módulo
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/fhrorganizaciones/backup/moodle2/backup_fhrorganizaciones_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the fhrorganizaciones instance
 *
 * @package   mod_fhrorganizaciones
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_fhrorganizaciones_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the fhrorganizaciones.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_fhrorganizaciones_activity_structure_step('fhrorganizaciones_structure', 'fhrorganizaciones.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of fhrorganizacioness.
        $search = '/('.$base.'\/mod\/fhrorganizaciones\/index.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@fhrorganizacionesINDEX*$2@$', $content);

        // Link to fhrorganizaciones view by moduleid.
        $search = '/('.$base.'\/mod\/fhrorganizaciones\/view.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@fhrorganizacionesVIEWBYID*$2@$', $content);

        return $content;
    }
}
