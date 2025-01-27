<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa Registradora</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Caixa Registradora</h1>

        <form action="" method="post">
            <label for="valor_compra">Valor da Compra (R$):</label>
            <input type="number" id="valor_compra" name="valor_compra" step="0.01" required>

            <label for="valor_pago">Valor Pago (R$):</label>
            <input type="number" id="valor_pago" name="valor_pago" step="0.01" required>

            <button type="submit" name="calcular">Calcular Troco</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $valor_compra = $_POST['valor_compra'];
            $valor_pago = $_POST['valor_pago'];
           
            if (!is_numeric($valor_compra) || !is_numeric($valor_pago)) {
                echo "<p class='error'>Por favor, insira valores numéricos válidos.</p>";
            } elseif ($valor_pago < $valor_compra) {
                echo "<p class='error'>O valor pago é menor que o valor da compra. Por favor, insira um valor maior.</p>";
            } else {
                
                $troco = $valor_pago - $valor_compra;
               
                $cedulas = [200, 100, 50, 20, 10, 5, 2];
                $quantidade_cedulas = [];

                
                foreach ($cedulas as $cedula) {
                    $quantidade_cedulas[$cedula] = floor($troco / $cedula);
                    $troco -= $quantidade_cedulas[$cedula] * $cedula;
                }

                
                echo "<p class='success'>Troco: R$ " . number_format($valor_pago - $valor_compra, 2, ',', '.') . "</p>";

                
                echo "<div class='result'><strong>Quantidade de cédulas para o troco:</strong><br>";
                foreach ($quantidade_cedulas as $cedula => $quantidade) {
                    if ($quantidade > 0) {
                        echo "{$quantidade} cédula(s) de R$ " . number_format($cedula, 2, ',', '.') . "<br>";
                    }
                }
                echo "</div>";
            }
        }
        ?>
    </div>

</body>
</html>
