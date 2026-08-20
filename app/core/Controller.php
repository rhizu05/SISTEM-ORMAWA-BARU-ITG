<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

abstract class Controller {

    protected $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    protected function requireLogin() {
        check_login();
    }

    protected function requireRole(array $roles) {
        check_role($roles);
    }

    protected function redirect($path) {
        redirect($path);
    }

    protected function sanitize($data) {
        return sanitize_input($this->conn, $data);
    }

    protected function addHistory($id_pengajuan, $id_user, $status, $catatan) {
        return add_history($this->conn, $id_pengajuan, $id_user, $status, $catatan);
    }

    /**
     * Mengirim respons JSON terstandar untuk endpoint AJAX.
     * Format: { success, message, data?, redirect? }
     */
    protected function jsonResponse(array $data, int $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
?>
