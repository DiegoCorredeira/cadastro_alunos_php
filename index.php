<?php
session_start();
function bancoDeDados()
{
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

function criandoTabelas($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS alunos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            idade INT NOT NULL, 
            curso VARCHAR(100) NOT NULL
        )";

    if ($conn->query($sql) !== true) {
        die('Erro ao criar tabela: ' . $conn->error);
    }
    return $conn;
}

function addAluno($conn, $nome, $idade, $curso)
{
    $sql = "INSERT INTO alunos (nome, idade, curso) VALUES ('$nome', '$idade', '$curso')";

    if ($conn->query($sql) !== true) {
        die('Erro ao adicionar aluno: ' . $conn->error);
    }
}

function delAluno($conn, $alunoId)
{
    $sql = "DELETE FROM alunos WHERE id=$alunoId";

    if ($conn->query($sql) !== true) {
        die('Erro ao deletar aluno: ' . $conn->error);
    }
}

function attAluno($conn, $alunoId, $nome, $idade, $curso)
{
    $sql = "UPDATE alunos SET nome='$nome', idade='$idade', curso='$curso' WHERE id='$alunoId'";

    if ($conn->query($sql) !== true) {
        die('Erro ao atualizar aluno: ' . $conn->error);
    }
}

function obterAluno($conn, $alunoId)
{
    $sql = "SELECT * FROM alunos WHERE id=$alunoId";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return new Aluno($row['id'], $row['nome'], $row['idade'], $row['curso']);
    }
    return null;
}


function obterAlunos($conn)
{
    $alunos = array();
    $sql = "SELECT * FROM alunos";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $aluno = new Aluno($row['id'], $row['nome'], $row['idade'], $row['curso']);
            $alunos[] = $aluno;
        }
    }
    return $alunos;
}



class Aluno
{
    public $id;
    public $nome;
    public $idade;
    public $curso;

    public function __construct($id, $nome, $idade, $curso)
    {
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

    if (isset($_POST['id'])){
        $alunoId = $_POST['id'];
        attAluno($conn, $alunoId, $nome, $idade, $curso);
        $_SESSION['msg'] = 'Aluno atualizado com sucesso';
    }else{
        addAluno($conn, $nome, $idade, $curso);

        $_SESSION['msg'] = 'Aluno cadastrado com sucesso';
    }
    $conn->close();

    header('Location: index.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'deletar' && isset($_GET['id'])) {
    $conn = bancoDeDados();
    criandoTabelas($conn);

    $alunoId = $_GET['id'];
    delAluno($conn, $alunoId);
    $_SESSION['msg'] = 'Aluno deletado com sucesso';

    $conn->close();

    header('Location: index.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'editar' && isset($_GET['id'])) {
    $conn = bancoDeDados();
    criandoTabelas($conn);

    $alunoId = $_GET['id'];
    $aluno = obterAluno($conn, $alunoId);
    $conn->close();
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
<style>
    body {
    font-family: Arial, Helvetica, sans-serif;
    background-color: #F4F4F4;
}

.container {
    max-width: 37.5rem;
    margin: 0 auto;
    padding: 1.2rem;
    background-color: #FFFFFF;
    border-radius: 5px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

h1, h2 {
    text-align: center;
}

form {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.3rem;
}

input[type="text"],
input[type="number"] {
    width: 100%;
    padding: 0.625rem;
    border: 1px solid #CCCCCC;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.8rem;
}

input[type="submit"],
button {
    align-items: center;
    justify-content: center;
    display: flex;
    background-color: #0080FF;
    color: #FFFFFF;
    padding: 0.6rem 1.6rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.8rem;
}

input[type="submit"]:hover,
button:hover {
    background-color: #45A049;
}

hr {
    border: none;
    border-top: 1px solid #CCCCCC;
    border-radius: 0.185rem;
    margin-bottom: 1.8rem;
}
.msg{
    text-align: center;
    margin-bottom: 10px;
    font-style: 50px;
    background-color: #d4edda;
    color: #155724;
    padding: 0.625rem;
    margin-bottom: 0.625rem;
    border-radius: 5px;
}

.aluno{
    padding: 0.625rem;
    background-color: #F4F4F4;
    border: 1px solid #CCCCCC;
    border-radius: 5px;
    margin-bottom: 0.625rem;
}
.aluno p {
    margin: 0;
}
a{
    color: #FF0000;
    text-decoration: none;
    margin-right: 10px;
}
#editar-button{
    color: #0080FF;
}

</style>
<body>
    <div class="container">
        <h1>Cadastro de Alunos simples com PHP</h1>
        <div class="msg">
            <?php if (!empty($_SESSION['msg'])) : ?>
                <?php echo $_SESSION['msg']; ?>
                <?php unset($_SESSION['msg']); ?>
            <?php endif; ?>
        </div>
        <?php if (isset($aluno)) : ?>
            <form id="cadastro-form" class="aluno-form" action="index.php" method="POST">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo $aluno->nome; ?>" required><br>
                <label for="idade">Idade:</label>
                <input type="number" id="idade" name="idade" value="<?php echo $aluno->idade; ?>" required><br>
                <label for="curso">Curso:</label>
                <input type="text" id="curso" name="curso" value="<?php echo $aluno->curso; ?>" required><br>

                <input type="hidden" name="id" value="<?php echo $aluno->id; ?>">

                <input type="submit" value="Salvar">
            </form>
        <?php else : ?>
            <form id="cadastro-form" class="aluno-form" action="index.php" method="POST">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required><br>
                <label for="idade">Idade:</label>
                <input type="number" id="idade" name="idade" required><br>
                <label for="curso">Curso:</label>
                <input type="text" id="curso" name="curso" required><br>

                <input type="submit" value="Adicionar">
            </form>
        <?php endif; ?>
        <h2>Lista de alunos</h2>
        <?php
        $conn = bancoDeDados();
        criandoTabelas($conn);

        $alunos = obterAlunos($conn);
        if (!empty($alunos)) {
            foreach ($alunos as $aluno) {
                echo "<div class='aluno'>";
                echo "<p><strong>Nome:</strong> {$aluno->nome}</p>";
                echo "<p><strong>Idade:</strong> {$aluno->idade}</p>";
                echo "<p><strong>Curso:</strong> {$aluno->curso}</p>";
                echo "<a href='index.php?action=deletar&id={$aluno->id}'>Deletar</a>";
                echo "<a href='index.php?action=editar&id={$aluno->id}' id='editar-button'>Editar</a>";
                echo "<hr>";
                echo "</div>";
            }
        } else {
            echo "<p>Nenhum aluno cadastrado</p>";
        }

        $conn->close();
        ?>

        <div>
            <button id="adicionar" onclick="mostraForm()">Adicionar</button><br>
        </div>
    </div>
    <script>
        function mostraForm() {
            let form = document.getElementById("cadastro-form");
            let addButton = document.getElementById("adicionar");

            if (form) {
                form.style.display = "block";
            }
            addButton.style.display = "none";
        }
    </script>
</body>

</html>
