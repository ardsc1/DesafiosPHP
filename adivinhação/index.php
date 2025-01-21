<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo da Adivinhação</title>
</head>
<body>
    <h1>Jogo da Adivinhação</h1>
    <?php
    session_start();

    if (!isset($_SESSION['numero_aleatorio'])) {
        $_SESSION['numero_aleatorio'] = rand(1, 100);
        $_SESSION['tentativas'] = 0;
        echo "<p>Um número entre 1 e 100 foi escolhido. Tente adivinhar!</p>";
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $palpite = filter_input(INPUT_POST, 'palpite', FILTER_VALIDATE_INT);

        if ($palpite === false || $palpite < 1 || $palpite > 100) {
            echo "<p>Por favor, insira um número válido entre 1 e 100.</p>";
        } else {
            $_SESSION['tentativas']++;
            $numero_aleatorio = $_SESSION['numero_aleatorio'];

            if ($palpite < $numero_aleatorio) {
                echo "<p>O número correto é maior que $palpite.</p>";
            } elseif ($palpite > $numero_aleatorio) {
                echo "<p>O número correto é menor que $palpite.</p>";
            } else {
                echo "<p>Parabéns! Você adivinhou o número $numero_aleatorio em {$_SESSION['tentativas']} tentativas.</p>";
                echo "<p><a href='?reset=1'>Jogar novamente</a></p>";
                session_destroy();
                exit;
            }
        }
    }


    ?>

    <form method="POST" action="">
        <label for="palpite">Digite seu palpite:</label>
        <input type="number" id="palpite" name="palpite" min="1" max="100" required>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
