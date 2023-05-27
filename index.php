<?php
 session_start();

 function bancoDeDados() {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $db = 'alunos';

 }







?>





<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de alunos - CRUD</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Sistema de Cadastro de alunos - CRUD</h1>
        <div class="msg"></div>
        <form action="index.php" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br>
            <label for="idade">Idade:</label>
            <input type="number" id="idade" name="idade" required><br>
            <label for="curso">Curso:</label>
            <input type="text" id="curso" name="curso" required><br>

            <input type="submit" value="Adicionar">
        </form>
        <h2>
            Lista de alunos
        </h2>
    </div>
</body>
</html>