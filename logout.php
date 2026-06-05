<?php
require_once('app.php');

session_unset();
session_destroy();

session_start();
app_set_flash('success', 'Byli jste odhlášeni.');
app_redirect('login.php');
