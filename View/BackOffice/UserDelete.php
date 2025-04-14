<?php
require_once __DIR__ . '/../../Controller/UserController.php';

if (isset($_GET['id'])) {
    $controller = new UserController();
    $controller->deleteUser($_GET['id']);
}
?>