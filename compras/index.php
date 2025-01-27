<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Carrinho de Compras</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Simulador de Carrinho de Compras</h1>
        <form action="" method="post">
            <label for="nome">Nome do Produto:</label>
            <input type="text" id="nome" name="nome" >

            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" min="1" >

            <label for="preco">Preço:</label>
            <input type="number" id="preco" name="preco" >

            <div class="button-group">
                <button type="submit" name="adicionar">Adicionar ao Carrinho</button>
                <button type="submit" name="finalizar">Finalizar Compra</button>
                
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
