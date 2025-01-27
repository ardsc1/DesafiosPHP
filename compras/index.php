<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Carrinho de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 0pz ;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
        .success {
            color: green;
            margin: 10px 0;
        }
        .clear-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
        .clear-button:hover {
            background-color: #a71d2a;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Simulador de Carrinho de Compras</h1>
        <form action="" method="post">
            <label for="nome">Nome do Produto:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" min="1" required>

            <label for="preco">Preço:</label>
            <input type="text" id="preco" name="preco" required>

            <div class="button-group">
                <button type="submit" name="adicionar">Adicionar ao Carrinho</button>
                <button type="submit" name="finalizar">Finalizar Compra</button>
                <a href="?limpar=true" class="clear-button">Limpar Carrinho</a>
            </div>
        </form>

        <?php
        session_start();

        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        function formatarPreco($preco) {
            return "R$ " . number_format($preco, 2, ',', '.');
        }

        if (isset($_POST['adicionar'])) {
            $nome = trim($_POST['nome']);
            $quantidade = (int)$_POST['quantidade'];
            $preco = str_replace(',', '.', trim($_POST['preco']));

            if (!empty($nome) && is_numeric($preco) && $preco > 0) {
                $_SESSION['carrinho'][] = [
                    'nome' => $nome,
                    'quantidade' => $quantidade,
                    'preco' => (float)$preco
                ];
                echo "<p class='success'>Produto adicionado com sucesso!</p>";
            } else {
                echo "<p class='error'>Por favor, insira dados válidos para o produto.</p>";
            }
        }

        if (!empty($_SESSION['carrinho'])) {
            echo "<table>
                <tr>
                    <th>Nome</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Total</th>
                </tr>";

            $totalGeral = 0;
            foreach ($_SESSION['carrinho'] as $item) {
                $total = $item['quantidade'] * $item['preco'];
                $totalGeral += $total;

                echo "<tr>
                    <td>{$item['nome']}</td>
                    <td>{$item['quantidade']}</td>
                    <td>" . formatarPreco($item['preco']) . "</td>
                    <td>" . formatarPreco($total) . "</td>
                </tr>";
            }

            echo "<tr>
                    <td colspan='3'><strong>Total Geral</strong></td>
                    <td><strong>" . formatarPreco($totalGeral) . "</strong></td>
                </tr>
            </table>";
        }

        if (isset($_POST['finalizar'])) {
            if (!empty($_SESSION['carrinho'])) {
                echo "<p class='success'>Compra finalizada com sucesso!</p>";
                echo "<a href='?limpar=true' class='clear-button'>Limpar Carrinho</a>";
            } else {
                echo "<p class='error'>O carrinho está vazio.</p>";
            }
        }

        if (isset($_GET['limpar']) && $_GET['limpar'] === 'true') {
            $_SESSION['carrinho'] = [];
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
        ?>
    </div>
</body>
</html>
