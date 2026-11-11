<?php

#seguinte leio o que ta em conecao.php antes de vir para cá
#area onde pegamos o resultado do php com o banco e trasformamos em coletions(acho que é assim o nome) para poder usar o 
# js como principal e conseguir ler os dados do php pegou.
require('conexao.php');

$sql = "SELECT * FROM lugares"; #no caso criar varios assim para cada tabela que presisas carregar dados no site
$sql1 = "SELECT * FROM tds";
$sql2 = "SELECT * FROM sdasd";
$sql3 = "SELECT * FROM bt";
$sql4 = "SELECT * FROM chapeu";

$resultado = mysqli_query($con, $sql);
$resultadotds = mysqli_query($con, $sql1); #php faz a coneção com o banco e guarda o resultado
$resultadoasdas = mysqli_query($con, $sql2);
$resultadobt = mysqli_query($con, $sql3);
$resultadochapeu = mysqli_query($con, $sql4); #nomes provisorios foi criado só para testar a logica

$dados = [];
$tds = [];
$asad = [];
$bt = [];
$chapeu = [];

while ($linha = mysqli_fetch_assoc($resultado)) { #string com cada resultado, lembra do exercicio do carcapio mesmo principio
    $dados[] = $linha;
}

while ($linha = mysqli_fetch_assoc($resultadotds)) {
    $tds[] = $linha;
}

while ($linha = mysqli_fetch_assoc($resultadoasdas)) {
    $asad[] = $linha;
}

while ($linha = mysqli_fetch_assoc($resultadobt)) {
    $bt[] = $linha;
}

while ($linha = mysqli_fetch_assoc($resultadochapeu)) {
    $chapeu[] = $linha;
}

$resposta = [
    "lugares" => $dados,
    "tds" => $tds,
    "asad" => $asad,
    "bt" => $bt,
    "chapeu" => $chapeu
];

echo json_encode($resposta);


/* usar no js quando for carregar algo no site (obs: não é para dar um control c e depois um V, é mais a ideia de como se vai usar)
 
fetch("buscar.php")
    .then(resposta => resposta.json())
    .then(dados => {
        console.log(dados.lugares);
        console.log(dados.tds);
        console.log(dados.asad);
        console.log(dados.bt);
        console.log(dados.chapeu);
    });

    */
?>