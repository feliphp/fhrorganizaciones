<?php
/*
@Autor: JFHR
@fecha:  2015-10-28
@Comentario: ***
* Importante y requerido para instalar el módulo
*/

namespace mod_fhrorganizaciones\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_fhrorganizaciones instance list viewed event class
 *
 * If the view mode needs to be stored as well, you may need to
 * override methods get_url() and get_legacy_log_data(), too.
 *
 * @package    mod_fhrorganizaciones
 * @copyright  2015 Your Name <your@email.adress>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Initialize the event
     */
    protected function init() {
        $this->data['objecttable'] = 'fhrorganizaciones';
        parent::init();
    }
}
