<?php
// Admin yetki kontrolü (tüm admin sayfalarında require et)
require_once __DIR__ . '/../includes/config.php';

function admin_kontrol() {
    if (empty($_SESSION['admin_giris'])) {
        header('Location: giris.php');
        exit;
    }
}

function admin_cikis() {
    unset($_SESSION['admin_giris']);
    header('Location: giris.php');
    exit;
}
