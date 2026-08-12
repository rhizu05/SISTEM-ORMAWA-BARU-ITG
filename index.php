<?php
/**
 * File: index.php
 * Deskripsi: Entry point aplikasi. Bootstrap dan delegasi ke Router.
 */

require_once 'config.php';

require_once ROOT_PATH . '/app/core/Controller.php';
require_once ROOT_PATH . '/app/controllers/UserController.php';
require_once ROOT_PATH . '/app/controllers/PengajuanController.php';
require_once ROOT_PATH . '/app/controllers/VerifikasiController.php';
require_once ROOT_PATH . '/app/controllers/BendaharaController.php';
require_once ROOT_PATH . '/app/controllers/ProfilController.php';
require_once ROOT_PATH . '/app/controllers/AspirasiController.php';
require_once ROOT_PATH . '/app/controllers/InformasiController.php';
require_once ROOT_PATH . '/app/core/Router.php';

start_output_buffering();

$router = new Router($conn);
$router->dispatch();
?>
