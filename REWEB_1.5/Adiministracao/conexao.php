<?php

    # vars de conexao
    $host = 'localhost';
    $db = 'test'; #nome do database
    $user = 'root';
    $password = ''; # configure a senha localmente; não publique credenciais
    $port = '3306'; ## padrao do MySQL é 3306

    # tenta fazer a conexao
    $con = mysqli_connect(
        $host,
        $user,
        $password,
        $db,
        $port
    );

    
    
    # echo '<pre>'; var_dump($con); echo '</pre>';

    ?>

