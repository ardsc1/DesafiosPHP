<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />

    <style>
      body {
        background: rgba(0, 0, 0, 0.7);
        font-family: Arial, sans-serif;
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    main {
        background-color: rgba(0, 0, 0, 0.7);
        border-radius: 10px;
        padding: 30px;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    h1 {
        font-size: 2rem;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #ffcc00;
    }

    p {
        margin: 15px 0;
        font-size: 1.1rem;
    }

    form {
        margin-top: 20px;
    }

    label {
        font-weight: bold;
        margin-bottom: 10px;
        display: block;
    }

    input[type="number"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1rem;
    }

    button {
        background-color: #ffcc00;
        color: #000;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
        font-weight: bold;
        text-transform: uppercase;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        width: 100%;
    }

    button:hover {
        background-color: #e6b800;
    }

    a {
        color: #00f2fe;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }
    
    </style>
    </head>

    <body class="">
    <main>
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
    </main>
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
