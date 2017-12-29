<?php
/*
@Autor: JFHR
@fecha:  2015-10-28
@Comentario: Archivo importante para la instalación
*/
function xmldb_fhrorganizaciones_install() {
	global $CFG, $OUTPUT, $DB;
	$DB->execute("INSERT INTO mdl_fhr_organizaciones (`id`, `name`, `parent_id`, `depth`, `path`, `timecreated`, `timemodified`, `grade`) VALUES
(1, 'SP y LC', 0, 1, '/1', 1446499141, 1446499141, 100),
(2, 'BAJÍO', 1, 2, '/1/2', 1446499153, 1446499153, 100),
(3, 'Administrativo  Bajío', 2, 3, '/1/2/3', 1446499169, 1446499169, 100),
(321, '433', 319, 4, '/1/300/319/321', 1446505670, 1446505670, 100)");
}

/**
 * Post installation recovery procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_fhrorganizaciones_install_recovery() {
}
