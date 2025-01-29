<?php
session_start();

// Modal de adicionar produto , adicionar coluna na tabel ou remover e atualizar coluna de opções

if (!isset($_SESSION['estoque'])) {
    $_SESSION['estoque'] = [];
}

function estoqueBaixo($quantidade) {
    return $quantidade < 5 ? " (Estoque Baixo)" : "";
}


if (isset($_POST['adicionar'])) {
    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $quantidade = intval($_POST['quantidade']);
    $preco = floatval($_POST['preco']);

    if (!isset($_SESSION['estoque'][$id])) {
        $_SESSION['estoque'][$id] = ["nome" => $nome, "categoria" => $categoria, "quantidade" => $quantidade, "preco" => $preco];
        $mensagem = "Produto adicionado com sucesso!";
    } else {
        $mensagem = "Erro: O ID do produto já existe!";
    }
}


if (isset($_POST['remover'])) {
    $id = intval($_POST['id_remover']);
    if (isset($_SESSION['estoque'][$id])) {
        unset($_SESSION['estoque'][$id]);
        $mensagem = "Produto removido com sucesso!";
    } else {
        $mensagem = "Erro: Produto não encontrado!";
    }
}


if (isset($_POST['atualizar'])) {
    $id = intval($_POST['id_atualizar']);
    $novaQuantidade = intval($_POST['nova_quantidade']);
    if (isset($_SESSION['estoque'][$id])) {
        $_SESSION['estoque'][$id]['quantidade'] = $novaQuantidade;
        $mensagem = "Quantidade atualizada com sucesso!";
    } else {
        $mensagem = "Erro: Produto não encontrado!";
    }
}


$precoMinimo = isset($_POST['filtrar_preco']) ? floatval($_POST['preco_minimo']) : null;


if (isset($_POST['calcular_total'])) {
    $categoria = $_POST['categoria_total'];
    $totalEstoque = 0;
    foreach ($_SESSION['estoque'] as $produto) {
        if ($produto['categoria'] === $categoria) {
            $totalEstoque += $produto['quantidade'] * $produto['preco'];
        }
    }
    $mensagem = "O valor total do estoque na categoria '$categoria' é R$ " . number_format($totalEstoque, 2, ',', '.');
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Estoque</title>
    <link rel="stylesheet" href="estoque.css">
</head>
<body>
    <div class="container">
        <h1>Gerenciamento de Estoque</h1>

        <?php if (isset($mensagem)): ?>
            <div class="mensagem"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <h2>Adicionar Produto</h2>
        <form method="POST">
            <input type="number" name="id" placeholder="ID do Produto" required>
            <input type="text" name="nome" placeholder="Nome do Produto" required>
            <input type="text" name="categoria" placeholder="Categoria" required>
            <input type="number" name="quantidade" placeholder="Quantidade" required>
            <input type="number" step="0.01" name="preco" placeholder="Preço (R$)" required>
            <button type="submit" name="adicionar">Adicionar</button>
        </form>

        <h2>Remover Produto</h2>
        <form method="POST">
            <input type="text" name="id_remover" placeholder="ID do Produto" required>
            <button type="submit" name="remover">Remover</button>
        </form>

        <h2>Atualizar Quantidade</h2>
        <form method="POST">
            <input type="text" name="id_atualizar" placeholder="ID do Produto" required>
            <input type="number" name="nova_quantidade" placeholder="Nova Quantidade" required>
            <button type="submit" name="atualizar">Atualizar</button>
        </form>

        <h2>Filtrar Produtos por Preço</h2>
        <form method="POST">
            <input type="number" step="0.01" name="preco_minimo" placeholder="Preço mínimo (R$)" required>
            <button type="submit" name="filtrar_preco">Filtrar</button>
        </form>

        <h2>Calcular Valor do Estoque por Categoria</h2>
        <form method="POST">
            <input type="text" name="categoria_total" placeholder="Categoria" required>
            <button type="submit" name="calcular_total">Calcular</button>
        </form>

        <h2>Estoque Atual</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Quantidade</th>
                    <th>Preço (R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['estoque'] as $id => $produto): ?>
                    <?php if (is_null($precoMinimo) || $produto['preco'] >= $precoMinimo): ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo $produto['nome']; ?></td>
                            <td><?php echo $produto['categoria']; ?></td>
                            <td>
                                <?php echo $produto['quantidade']; ?>
                                <span class="alerta"><?php echo estoqueBaixo($produto['quantidade']); ?></span>
                            </td>
                            <td><?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
