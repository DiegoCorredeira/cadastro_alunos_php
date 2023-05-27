<?php
 session_start();

    function bancoDeDados() {
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $db = 'alunos';


        $conn = new mysqli($host, $user, $password, $db);

        if ($conn->connect_error) {
            die('Falha na conexão com o banco de dados: ' . $conn->connect_error);
        }
        return $conn;

    }

    function criandoTabelas($conn){
        $sql = "CREATE TABLE IF NOT EXISTS alunos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            idade INT NOT NULL, 
            curso VARCHAR(100) NOT NULL
            )";

        if ($conn->query($sql) === true) {
            echo "Tabela criada com sucesso!";
        } else {
            echo "Erro ao criar tabela: " . $conn->error;
        }
    }


    function addAluno($conn, $nome, $idade, $curso) {
        $sql = "INSERT INTO alunos (nome, idade, curso) VALUES ('$nome', '$idade', '$curso')";

        if ($conn->query($sql) !== true) {
            die('Erro ao adicionar aluno: ' . $conn->error);
        }
    }

    function attAluno($conn, $id, $nome, $idade, $curso) {
        $sql = "UPDATE alunos SET nome='$nome', idade='$idade', curso='$curso' WHERE id='$id'";

        if ($conn->query($sql) !== true) {
            die('Erro ao atualizar aluno: ' . $conn->error);
        }
    }

    class Aluno {
        public $id;
        public $nome;
        public $idade;
        public $curso;
    
        public function __construct($id, $nome, $idade, $curso) {
            $this->id = $id;
            $this->nome = $nome;
            $this->idade = $idade;
            $this->curso = $curso;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $conn = bancoDeDados();
        criandoTabelas($conn);
    
        $nome = $_POST['nome'];
        $idade = $_POST['idade'];
        $curso = $_POST['curso'];
    
        if (isset($_POST['aluno_id'])) {
            $alunoId = $_POST['aluno_id'];
            attAluno($conn, $alunoId, $nome, $idade, $curso);
            $_SESSION['mensagem'] = 'Aluno atualizado com sucesso';
        } else {
            addAluno($conn, $nome, $idade, $curso);
            $_SESSION['mensagem'] = 'Aluno adicionado com sucesso';
        }
    
        $conn->close();
    
        header('Location: index.php');
        exit();
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