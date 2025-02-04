<?php
session_start();

if (!isset($_SESSION['estoque'])) {
    $_SESSION['estoque'] = [];
}
if (!isset($_SESSION['next_id'])) {
    $_SESSION['next_id'] = 1;
}

function estoqueBaixo($quantidade) {
    return $quantidade < 5 ? " (Estoque Baixo)" : "";
}

if (isset($_POST['adicionar'])) {
    $id = $_SESSION['next_id'];
    $_SESSION['next_id']++; 
    
    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $quantidade = intval($_POST['quantidade']);
    $preco = floatval($_POST['preco']);

    $_SESSION['estoque'][$id] = [
        "nome" => $nome, 
        "categoria" => $categoria, 
        "quantidade" => $quantidade, 
        "preco" => $preco
    ];
}

if (isset($_POST['remover'])) {
    $id = intval($_POST['id_remover']);
    if (isset($_SESSION['estoque'][$id])) {
        unset($_SESSION['estoque'][$id]);
    } else {
        $mensagem = "Erro: Produto não encontrado!";
    }
}

if (isset($_POST['atualizar'])) {
    $id = intval($_POST['id_atualizar']);
    $novaQuantidade = intval($_POST['nova_quantidade']);
    $novoNome = $_POST['novo_nome'];
    $novaCategoria = $_POST['nova_categoria'];
    $novoPreco = floatval($_POST['novo_preco']);
    
    if (isset($_SESSION['estoque'][$id])) {
        $_SESSION['estoque'][$id]['quantidade'] = $novaQuantidade;
        $_SESSION['estoque'][$id]['nome'] = $novoNome;
        $_SESSION['estoque'][$id]['categoria'] = $novaCategoria;
        $_SESSION['estoque'][$id]['preco'] = $novoPreco;

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
//recarregar a pagina dps aparecer o sweetalert
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
  <link rel="stylesheet" href="estoque.css?v=<?=time(); ?>">

</head>
<body>
  <div id="principal">
    <div class="container p-0 mt-2 mb-2">
      <h1>Gerenciamento de Estoque</h1>
      <?php if (isset($mensagem)): ?>
        <div class="mensagem"><?= $mensagem; ?></div>
      <?php endif; ?>
      
      <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#modalAdicionar">Adicionar Produto</button>
     
      <div class="modal fade" id="modalAdicionar" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalLabel">Adicionar Produto</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="formAdicionar" method="POST">
                <input type="hidden" name="adicionar" value="1" required>
                <label for="nome">Nome do Produto</label>
                <input type="text" id="nome" name="nome" class="form-control mb-2" required>
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" class="form-control mb-2" required>
                  <option value="eletronico">Eletronicos</option>
                  <option value="alimentos">Alimentos</option>
                  <option value="materiais">Materiais</option>
                  <option value="ferramentas">Ferramentas</option>
                </select>
                <label for="quantidade">Quantidade</label>
                <input type="number" id="quantidade" name="quantidade" class="form-control mb-2" required>
                <label for="preco">Preço (R$)</label>
                <input type="number" step="0.01" id="preco" name="preco" class="form-control mb-2" required>
                <button type="button" onclick="adicionarProduto()" class="btn btn-success">Adicionar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      
      <form method="POST">
        <label for="" class="fs-2">Filtrar produto por Preço</label>
        <input type="number" step="0.01" name="preco_minimo" placeholder="Preço mínimo (R$)" required>
        <button class="btn btn-primary mt-2" type="submit" name="filtrar_preco">Filtrar</button>
      </form>
     
     
      <h2>Estoque Atual</h2>
      <div class="tabela-container">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Quantidade</th>
                <th>Preço (R$)</th>
                <th>Total (R$)</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($_SESSION['estoque'] as $id => $produto): ?>
                <?php if (is_null($precoMinimo) || $produto['preco'] >= $precoMinimo): ?>
                  <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $produto['nome']; ?></td>,
                    <td><?php echo $produto['categoria']; ?></td>
                    <td>
                      <?php echo $produto['quantidade']; ?>
                      <span class="alerta"><?php echo estoqueBaixo($produto['quantidade']); ?></span>
                    </td>
                    <td><?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($produto['quantidade'] * $produto['preco'], 2, ',', '.'); ?></td>
                    <td>
                      <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar<?php echo $id; ?>">Editar</button>
                      <button class="btn btn-danger btn-sm" onclick="confirmarRemocao(<?php echo $id; ?>)">Remover</button>
                    </td>
                  </tr>
                            
                  <div class="modal fade" id="modalEditar<?php echo $id; ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?php echo $id; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modalEditarLabel<?php echo $id; ?>">Editar Produto</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <form id="formAtualizar_<?php echo $id; ?>" method="POST">
                            <input type="hidden" name="atualizar" value="1">
                            <input type="hidden" name="id_atualizar" value="<?php echo $id; ?>">
                            <label for="novo_nome_<?php echo $id; ?>">Novo Nome:</label>
                            <input type="text" id="novo_nome_<?php echo $id; ?>" name="novo_nome" class="form-control mb-2" required value="<?php echo $produto['nome']; ?>">
                            <label for="nova_categoria_<?php echo $id; ?>">Nova Categoria:</label>
                            <select id="nova_categoria_<?php echo $id; ?>" name="nova_categoria" class="form-control mb-2" required>
                              <option value="eletronico" <?php echo ($produto['categoria'] == 'eletronico') ? 'selected' : ''; ?>>Eletronicos</option>
                              <option value="alimentos" <?php echo ($produto['categoria'] == 'alimentos') ? 'selected' : ''; ?>>Alimentos</option>
                              <option value="materiais" <?php echo ($produto['categoria'] == 'materiais') ? 'selected' : ''; ?>>Materiais</option>
                              <option value="ferramentas" <?php echo ($produto['categoria'] == 'ferramentas') ? 'selected' : ''; ?>>Ferramentas</option>
                            </select>
          
                            <label for="nova_quantidade_<?php echo $id; ?>">Nova Quantidade</label>
                            <input type="number" id="nova_quantidade_<?php echo $id; ?>" name="nova_quantidade" class="form-control mb-2" required>
          
          
                            <label for="novo_preco_<?php echo $id; ?>">Novo Preço (R$)</label>
                            <input type="number" step="0.01" id="novo_preco_<?php echo $id; ?>" name="novo_preco" class="form-control mb-2" required value="<?php echo $produto['preco']; ?>">
          
                            <button type="button" onclick="atualizarProduto(<?php echo $id; ?>)" class="btn btn-success">Atualizar</button>
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
  </div>

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

    function adicionarProduto() {
      Swal.fire({
        position: "top-end",
        icon: "success",
        title: "Produto Adicionado",
        showConfirmButton: false,
        timer: 1500
      }).then(() => {
        document.getElementById("formAdicionar").submit();
      });
    }

    function atualizarProduto(id) {
      Swal.fire({
        position: "top-end",
        icon: "success",
        title: "Edição Concluída",
        showConfirmButton: false,
        timer: 1500
      }).then(() => {
        document.getElementById("formAtualizar_" + id).submit();
      });
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
