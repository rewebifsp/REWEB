<?php

 @session_start();
 @session_unset();  // Remove todas as variáveis de sessã
 @session_destroy(); 

     header( 'Location: ../index.php');
exit;

//resumo(preguiça de esxplicar os passo, pega a sesão atual, remove todas e destroi elas e joga de volta para o index)