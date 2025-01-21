<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Carrinho de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

<h1>Simulador de Carrinho de Compras</h1>

<?php
session_start();


if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $nome = htmlspecialchars($_POST['nome']);
    $quantidade = (int) $_POST['quantidade'];
    $preco = (float) $_POST['preco'];

    if ($nome && $quantidade > 0 && $preco > 0) {
        $_SESSION['carrinho'][] = [
            'nome' => $nome,
            'quantidade' => $quantidade,
            'preco' => $preco
        ];
        echo "<p style='color: green;'>Item adicionado ao carrinho com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>Por favor, preencha todos os campos corretamente.</p>";
    }
}

// Finalizar compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    $total = 0;
    echo "<h2>Resumo do Carrinho</h2>";
    if (!empty($_SESSION['carrinho'])) {
        echo "<table>
                <tr>
                    <th>Nome</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                </tr>";
        foreach ($_SESSION['carrinho'] as $item) {
            $subtotal = $item['quantidade'] * $item['preco'];
            $total += $subtotal;
            echo "<tr>
                    <td>{$item['nome']}</td>
                    <td>{$item['quantidade']}</td>
                    <td>R$ " . number_format($item['preco'], 2, ',', '.') . "</td>
                    <td>R$ " . number_format($subtotal, 2, ',', '.') . "</td>
                </tr>";
        }
        echo "<tr>
                <td colspan='3'><strong>Total</strong></td>
                <td><strong>R$ " . number_format($total, 2, ',', '.') . "</strong></td>
            </tr>";
        echo "</table>";
        // Limpar carrinho após finalizar
        $_SESSION['carrinho'] = [];
    } else {
        echo "<p>O carrinho está vazio.</p>";
    }
}
?>

<form method="POST">
    <h2>Adicionar Item ao Carrinho</h2>
    <label for="nome">Nome do Produto:</label><br>
    <input type="text" id="nome" name="nome" required><br><br>

    <label for="quantidade">Quantidade:</label><br>
    <input type="number" id="quantidade" name="quantidade" min="1" required><br><br>

    <label for="preco">Preço Unitário:</label><br>
    <input type="number" id="preco" name="preco" step="0.01" min="0" required><br><br>

    <button type="submit" name="adicionar">Adicionar ao Carrinho</button>
    <button type="submit" name="finalizar">Finalizar Compra</button>
</form>

<?php if (!empty($_SESSION['carrinho'])): ?>
    <h2>Itens no Carrinho</h2>
    <table>
        <tr>
            <th>Nome</th>
            <th>Quantidade</th>
            <th>Preço Unitário</th>
            <th>Subtotal</th>
        </tr>
        <?php
        $total = 0;
        foreach ($_SESSION['carrinho'] as $item) {
            $subtotal = $item['quantidade'] * $item['preco'];
            $total += $subtotal;
            echo "<tr>
                    <td>{$item['nome']}</td>
                    <td>{$item['quantidade']}</td>
                    <td>R$ " . number_format($item['preco'], 2, ',', '.') . "</td>
                    <td>R$ " . number_format($subtotal, 2, ',', '.') . "</td>
                </tr>";
        }
        ?>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></td>
        </tr>
    </table>
<?php endif; ?>

</body>
</html>
