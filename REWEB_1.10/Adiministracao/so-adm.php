<?php
// Coloque este arquivo no começo de toda página de administração:
//   require('so-adm.php');
// Se a pessoa não for ADM, ela volta para a tela inicial antes de ver qualquer coisa.
@session_start();

if (!isset($_SESSION['usuario']['tipo']) || $_SESSION['usuario']['tipo'] != 'adm') {
    header('Location: ../index.php');
    exit;
}
