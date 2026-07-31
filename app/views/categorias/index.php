<?php
/**
 * Finzy — View de Gestão de Categorias (app/views/categorias/index.php)
 * 
 * Interface visual para cadastro, listagem, filtragem e edição de categorias financeiras.
 * Segue o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Gestão de Categorias — Finzy';
require __DIR__ . '/../layouts/header.php';
?>

<main class="container py-4 flex-grow-1">
    <!-- Cabeçalho da Página -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Categorias Financeiras</h1>
            <p class="text-muted mb-0">Gerencie a classificação de receitas e despesas do sistema.</p>
        </div>
        <div>
            <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCategoria" onclick="limparFormularioCategoria()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                </svg>
                Nova Categoria
            </button>
        </div>
    </div>

    <!-- Alertas de Feedback -->
    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <strong>Atenção!</strong> <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <?php
                if ($_GET['sucesso'] === 'criado') echo '<strong>Sucesso!</strong> Nova categoria cadastrada com êxito.';
                elseif ($_GET['sucesso'] === 'atualizado') echo '<strong>Sucesso!</strong> Categoria atualizada com êxito.';
                elseif ($_GET['sucesso'] === 'status') echo '<strong>Sucesso!</strong> Status da categoria alterado com êxito.';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Card de Filtros -->
    <div class="card border rounded-3 bg-white shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="index.php" class="row g-3 align-items-end">
                <input type="hidden" name="route" value="categorias">

                <div class="col-12 col-md-5">
                    <label for="filtroBusca" class="form-label fw-semibold text-secondary small text-uppercase">Buscar por Nome</label>
                    <input type="text" class="form-control" id="filtroBusca" name="busca" placeholder="Ex: Alimentação, Salário..." value="<?php echo htmlspecialchars($filtros['busca'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filtroTipo" class="form-label fw-semibold text-secondary small text-uppercase">Tipo</label>
                    <select class="form-select" id="filtroTipo" name="tipo">
                        <option value="">Todos os Tipos</option>
                        <option value="receita" <?php echo ($filtros['tipo'] ?? '') === 'receita' ? 'selected' : ''; ?>>Receita</option>
                        <option value="despesa" <?php echo ($filtros['tipo'] ?? '') === 'despesa' ? 'selected' : ''; ?>>Despesa</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label for="filtroStatus" class="form-label fw-semibold text-secondary small text-uppercase">Status</label>
                    <select class="form-select" id="filtroStatus" name="status">
                        <option value="">Todos</option>
                        <option value="ativo" <?php echo ($filtros['status'] ?? '') === 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                        <option value="inativo" <?php echo ($filtros['status'] ?? '') === 'inativo' ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">Filtrar</button>
                    <a href="?route=categorias" class="btn btn-outline-secondary" title="Limpar Filtros">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Categorias -->
    <div class="card border rounded-3 bg-white shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom" style="background-color: var(--color-surface-container-low) !important;">
                    <tr>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Nome</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Tipo</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Status</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Auditoria</th>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categorias)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-tag text-muted mb-3 opacity-50" viewBox="0 0 16 16">
                                        <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z"/>
                                        <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V1zm3.586 0H2v3.586l7 7L12.586 8l-7-7z"/>
                                    </svg>
                                    <p class="h6 text-secondary mb-2">Nenhuma categoria encontrada.</p>
                                    <p class="text-muted small mb-3">Tente ajustar os filtros de busca ou cadastre uma nova categoria.</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCategoria" onclick="limparFormularioCategoria()">
                                        Cadastrar Categoria
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-dark">
                                    <?php echo htmlspecialchars($cat['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-3 py-3">
                                    <?php if ($cat['tipo'] === 'receita'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            Receita
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            Despesa
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-3">
                                    <?php echo FormatHelper::statusBadge($cat['status']); ?>
                                </td>
                                <td class="px-3 py-3 text-muted small">
                                    <div>Criado por <strong><?php echo htmlspecialchars($cat['criador_nome'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?></strong> em <?php echo FormatHelper::dataHora($cat['criado_em']); ?></div>
                                    <?php if (!empty($cat['alterado_em'])): ?>
                                        <div class="text-secondary" style="font-size: 0.75rem;">Editado por <?php echo htmlspecialchars($cat['alterado_nome'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?> em <?php echo FormatHelper::dataHora($cat['alterado_em']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editarCategoria(<?php echo htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8'); ?>)"
                                                title="Editar Categoria">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                            </svg>
                                        </button>

                                        <form method="POST" action="?route=categorias_status" class="d-inline" onsubmit="return confirm('Deseja realmente <?php echo $cat['status'] === 'ativo' ? 'inativar' : 'ativar'; ?> esta categoria?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="id" value="<?php echo (int)$cat['id']; ?>">
                                            <input type="hidden" name="status" value="<?php echo $cat['status'] === 'ativo' ? 'inativo' : 'ativo'; ?>">
                                            
                                            <?php if ($cat['status'] === 'ativo'): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Inativar Categoria">
                                                    Inativar
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Ativar Categoria">
                                                    Ativar
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal de Cadastro/Edição de Categoria -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="background-color: var(--color-primary) !important;">
                <h5 class="modal-title fw-bold" id="modalCategoriaLabel">Nova Categoria</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="POST" action="?route=categorias_salvar">
                <div class="modal-body p-4">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" id="categoriaId" value="">

                    <div class="mb-3">
                        <label for="categoriaNome" class="form-label fw-semibold">Nome da Categoria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoriaNome" name="nome" placeholder="Ex: Alimentação, Vendas, Moradia..." required maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="categoriaTipo" class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="categoriaTipo" name="tipo" required>
                            <option value="">Selecione o Tipo...</option>
                            <option value="receita">Receita</option>
                            <option value="despesa">Despesa</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="categoriaStatus" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="categoriaStatus" name="status" required>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4" style="background-color: var(--color-primary); border-color: var(--color-primary);">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function limparFormularioCategoria() {
    document.getElementById('modalCategoriaLabel').textContent = 'Nova Categoria';
    document.getElementById('categoriaId').value = '';
    document.getElementById('categoriaNome').value = '';
    document.getElementById('categoriaTipo').value = '';
    document.getElementById('categoriaStatus').value = 'ativo';
}

function editarCategoria(cat) {
    document.getElementById('modalCategoriaLabel').textContent = 'Editar Categoria';
    document.getElementById('categoriaId').value = cat.id;
    document.getElementById('categoriaNome').value = cat.nome;
    document.getElementById('categoriaTipo').value = cat.tipo;
    document.getElementById('categoriaStatus').value = cat.status;
    
    var modal = new bootstrap.Modal(document.getElementById('modalCategoria'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
