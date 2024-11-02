<?php

function lerTarefas() {
    $tarefas = [];
    if (file_exists("tarefas.txt")) {
        $linhas = file("tarefas.txt", FILE_IGNORE_NEW_LINES);
        foreach ($linhas as $linha) {
            list($id, $titulo, $descricao) = explode("|", $linha);
            $tarefas[] = ['id' => $id, 'titulo' => $titulo, 'descricao' => $descricao];
        }
    }
    return $tarefas;
}


function salvarTarefas($tarefas) {
    $conteudo = "";
    foreach ($tarefas as $tarefa) {
        $conteudo .= $tarefa['id'] . "|" . $tarefa['titulo'] . "|" . $tarefa['descricao'] . PHP_EOL;
    }
    file_put_contents("tarefas.txt", $conteudo);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == "adicionar") {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $tarefas = lerTarefas();
    $id = count($tarefas) + 1;
    $tarefas[] = ['id' => $id, 'titulo' => $titulo, 'descricao' => $descricao];
    salvarTarefas($tarefas);
    header("Location: index.php");
    exit();
}


if (isset($_GET['excluir'])) {
    $idExcluir = $_GET['excluir'];
    $tarefas = lerTarefas();
    $tarefas = array_filter($tarefas, function($tarefa) use ($idExcluir) {
        return $tarefa['id'] != $idExcluir;
    });
    salvarTarefas($tarefas);
    header("Location: index.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == "atualizar") {
    $idAtualizar = $_POST['id'];
    $novoTitulo = $_POST['titulo'];
    $novaDescricao = $_POST['descricao'];
    $tarefas = lerTarefas();
    foreach ($tarefas as &$tarefa) {
        if ($tarefa['id'] == $idAtualizar) {
            $tarefa['titulo'] = $novoTitulo;
            $tarefa['descricao'] = $novaDescricao;
            break;
        }
    }
    salvarTarefas($tarefas);
    header("Location: index.php");
    exit();
}

$tarefas = lerTarefas();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-do List</title>
</head>
<body>
    <h1>To-do List</h1>

    <?php include 'form.html'; ?>

    <h2>Tarefas:</h2>
    <ul>
        <?php foreach ($tarefas as $tarefa): ?>
            <li>
                <?php echo $tarefa['id'] . " | " . $tarefa['titulo'] . " | " . $tarefa['descricao']; ?>
                <a href="index.php?excluir=<?php echo $tarefa['id']; ?>">Excluir</a>
                
                <form action="index.php" method="post" style="display:inline;">
                    <input type="hidden" name="acao" value="atualizar">
                    <input type="hidden" name="id" value="<?php echo $tarefa['id']; ?>">
                    <input type="text" name="titulo" value="<?php echo $tarefa['titulo']; ?>">
                    <input type="text" name="descricao" value="<?php echo $tarefa['descricao']; ?>">
                    <button type="submit">Atualizar</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
