// Corrigir erro de deletar e editar estudante.

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
    $sql = "DELETE FROM alunos WHERE id='$alunoId'";

    if ($conn->query($sql) !== true) {
        die('Erro ao deletar aluno: ' . $conn->error);
    }
}

function attAluno($conn, $id, $nome, $idade, $curso)
{
    $sql = "UPDATE alunos SET nome='$nome', idade='$idade', curso='$curso' WHERE id='$id'";

    if ($conn->query($sql) !== true) {
        die('Erro ao atualizar aluno: ' . $conn->error);
    }
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

    addAluno($conn, $nome, $idade, $curso);

    $_SESSION['msg'] = 'Aluno cadastrado com sucesso';

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

<body>
    <style>
        .msg {
            padding: 10px;
            background-color: #E0F2FF;
            border: 1px solid #0080FF;
            color: #0080FF;
            margin-bottom: 20px;
            text-align: center;
            display: <?php echo !empty($msg) ? 'block' : 'none;' ?>
        }
    </style>
    <div class="container">
        <h1>Sistema de Cadastro de alunos - CRUD</h1>
        <div class="msg">
            <?php if (!empty($msg)) : ?>
                <p><?= $msg ?></p>
            <?php endif; ?>
        </div>
        <form id="cadastro-form" action="index.php" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br>
            <label for="idade">Idade:</label>
            <input type="number" id="idade" name="idade" required><br>
            <label for="curso">Curso:</label>
            <input type="text" id="curso" name="curso" required><br>

            <input type="submit" value="Salvar">
        </form>
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
                echo "<a href=index.php?action=deletar&id='{$aluno->id}'>deletar</a>";
                echo "<a href=index.php?action=editar&id='{$aluno->id}'>editar</a>";
                echo "</div>";
            }
        } else {
            echo "<p>Nenhum aluno cadastrado</p>";
        }

        $conn->close();



        ?>

        <div>
            <button id="adicionar" onclick="mostraForm()">Adicionar</button><br>
            <button id="editar" onclick="mostraForm()">Editar</button>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>
