<?php

    # vars de conexao
    $host = 'localhost';
    $db = 'test';
    $user = 'root';
    $password = ''; #não esquecer de colocar senha como "ifsp" se for apresentar no pc da escola, se for da julia a senha do workbenche dela
    $port = '3306'; ## padrao do MySQL é 3306

    # tenta fazer a conexao
    $con = mysqli_connect(
        $host,
        $user,
        $password,
        $db,
        $port
    );

    #seguinte rapaziada fazer uma conexão direta com o banco pelo que vi é só com o php e usar.js presisa instalar negocio para o javascripte rodar fora do navegador
    #pelo que to vendo por cima/pesquisando/fazendo o idela seria usar o php em toda parte que tem o banco de dado, mas
    #como foi pedido usar o minimo possivel de php e usar mais o js, o code vai virar um espaguete desnecessario só para o js ser o principal
    #apos ganhar nota do projeto vamo ter que pegar e modificar bastante coisa para deixar as coisas mais simples ao longo prazo
    
    
    # echo '<pre>'; var_dump($con); echo '</pre>';

    ?>

