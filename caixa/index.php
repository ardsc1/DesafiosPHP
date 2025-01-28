<?php
session_start();

function inicializarCaixa() {
    return [
        200 => 5,
        100 => 7,
        50 => 10,
        20 => 15,
        10 => 20,
        5 => 25,
        2 => 30
    ];
}

if (!isset($_SESSION['caixa'])) {
    $_SESSION['caixa'] = inicializarCaixa();
}

function calcularTroco($valorCompra, $valorPago, &$caixa) {
    $troco = $valorPago - $valorCompra;
    if ($troco < 0) {
        return "Valor pago é insuficiente. Por favor, insira um valor maior ou igual ao valor da compra.";
    }

    $cedulasTroco = [];
    foreach ($caixa as $cedula => $quantidade) {
        if ($troco >= $cedula && $quantidade > 0) {
            $numCedulas = min(intdiv($troco, $cedula), $quantidade);
            if ($numCedulas > 0) {
                $cedulasTroco[$cedula] = $numCedulas;
                $troco -= $numCedulas * $cedula;
                $caixa[$cedula] -= $numCedulas;
            }
        }
    }

    if ($troco > 0) {
        return "Não há cédulas suficientes para dar o troco exato.";
    }

    return $cedulasTroco;
}

function calcularTotalCaixa($caixa) {
    $total = 0;
    foreach ($caixa as $cedula => $quantidade) {
        $total += $cedula * $quantidade;
    }
    return $total;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['reset'])) {
        $_SESSION['caixa'] = inicializarCaixa();
        $mensagem = "Caixa restaurado com sucesso!";
    } else {
        $valorCompra = floatval($_POST['valor_compra']);
        $valorPago = floatval($_POST['valor_pago']);

        if (!is_numeric($valorCompra) || !is_numeric($valorPago)) {
            $mensagem = "Por favor, insira valores numéricos válidos.";
        } else {
            $resultado = calcularTroco($valorCompra, $valorPago, $_SESSION['caixa']);
            if (is_array($resultado)) {
                $mensagem = "Troco: <br>";
                foreach ($resultado as $cedula => $quantidade) {
                    $mensagem .= "$quantidade nota(s) de R$ $cedula <br>";
                }
            } else {
                $mensagem = $resultado;
            }
        }
    }
}

$totalCaixa = calcularTotalCaixa($_SESSION['caixa']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa Registradora</title>
    <link rel="stylesheet" href="caixa.css">
</head>
<body>
    <div class="container">
        <h1>Caixa Registradora</h1>
        <form method="POST">
            <label for="valor_compra">Valor da Compra:</label>
            <input type="text" id="valor_compra" name="valor_compra">
            <br>
            <label for="valor_pago">Valor Pago:</label>
            <input type="text" id="valor_pago" name="valor_pago">
            <br>
            <button type="submit" name="calcular">Calcular Troco</button>
            <button type="submit" name="reset">Resetar Caixa</button>
        </form>

        <?php if (!empty($mensagem)): ?>
            <div class="resultado">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <h2>Estado atual do caixa:</h2>
        <ul>
            <?php foreach ($_SESSION['caixa'] as $cedula => $quantidade): ?>
                <li>R$ <?php echo $cedula; ?>: <?php echo $quantidade; ?> notas</li>
            <?php endforeach; ?>
        </ul>

        <h3>Total no caixa: R$ <?php echo number_format($totalCaixa, 2, ',', '.'); ?></h3>
    </div>
</body>
</html>
