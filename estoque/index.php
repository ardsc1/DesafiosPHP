<?php
session_start();


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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Estoque</title>
    <link rel="stylesheet" href="estoque.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div id="principal">
        <div class="container p-0 mt-2 mb-2">
            <h1>Gerenciamento de Estoque</h1>
            <?php if (isset($mensagem)): ?>
                <div class="mensagem"><?php echo $mensagem; ?></div>
            <?php endif; ?>

            
            <h2>Adicionar Produto</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionar">Adicionar Produto</button>

            
            <div class="modal fade" id="modalAdicionar" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">Adicionar Produto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <input type="number" name="id" class="form-control mb-2" placeholder="ID do Produto" required>
                                <input type="text" name="nome" class="form-control mb-2" placeholder="Nome do Produto" required>
                                <input type="text" name="categoria" class="form-control mb-2" placeholder="Categoria" required>
                                <input type="number" name="quantidade" class="form-control mb-2" placeholder="Quantidade" required>
                                <input type="number" step="0.01" name="preco" class="form-control mb-2" placeholder="Preço (R$)" required>
                                <button type="submit" name="adicionar" class="btn btn-success">Adicionar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            
            <h2 class="mt-3">Filtrar Produtos por Preço</h2>
            <form method="POST">
                <input type="number" step="0.01" name="preco_minimo" placeholder="Preço mínimo (R$)" required>
                <button class="btn btn-primary mt-1" type="submit" name="filtrar_preco">Filtrar</button>
            </form>

            
            <h2>Calcular Valor do Estoque por Categoria</h2>
            <form method="POST">
                <input type="text" name="categoria_total" placeholder="Categoria" required>
                <button type="submit" class="btn btn-primary mt-1" name="calcular_total">Calcular</button>
            </form>

            
            <h2>Estoque Atual</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Quantidade</th>
                        <th>Preço (R$)</th>
                        <th>Ações</th>
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
                                <td>                                   
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar<?php echo $id; ?>">Editar</button>
                                    
                                    <button class="btn btn-danger btn-sm" onclick="confirmarRemocao(<?php echo $id; ?>)">Remover</button>
                                </td>
                            </tr>

                            
                            <div class="modal fade" id="modalEditar<?php echo $id; ?>" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel">Editar Quantidade</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id_atualizar" value="<?php echo $id; ?>">
                                                <input type="number" name="nova_quantidade" class="form-control mb-2" placeholder="Nova Quantidade" required>
                                                <button type="submit" name="atualizar" class="btn btn-success">Atualizar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarRemocao(id) {
            Swal.fire({
                title: "Tem certeza que deseja remover este produto?",
                text: "Essa ação não pode ser desfeita!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sim, remover!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    let form = document.createElement('form');
                    form.method = "POST";
                    form.style.display = "none";

                    let input = document.createElement('input');
                    input.type = "hidden";
                    input.name = "id_remover";
                    input.value = id;

                    let button = document.createElement('input');
                    button.type = "hidden";
                    button.name = "remover";
                    button.value = "1";

                    form.appendChild(input);
                    form.appendChild(button);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>