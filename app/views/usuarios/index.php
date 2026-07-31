<?php
/**
 * Finzy — View de Gestão de Usuários (app/views/usuarios/index.php)
 * 
 * Interface visual para gerenciamento de usuários do sistema. Restrito a Administradores.
 * Segue o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Gestão de Usuários — Finzy';
require __DIR__ . '/../layouts/header.php';
?>

<main class="container py-4 flex-grow-1">
    <!-- Cabeçalho da Página -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Gestão de Usuários</h1>
            <p class="text-muted mb-0">Cadastre, edite e controle os perfis e acessos dos usuários ao sistema.</p>
        </div>
        <div>
            <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="limparFormularioUsuario()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person-plus-fill" viewBox="0 0 16 16">
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                    <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
                </svg>
                Novo Usuário
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
                if ($_GET['sucesso'] === 'criado') echo '<strong>Sucesso!</strong> Novo usuário cadastrado com êxito.';
                elseif ($_GET['sucesso'] === 'atualizado') echo '<strong>Sucesso!</strong> Dados do usuário atualizados com êxito.';
                elseif ($_GET['sucesso'] === 'status') echo '<strong>Sucesso!</strong> Status do usuário alterado com êxito.';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Card de Filtros -->
    <div class="card border rounded-3 bg-white shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="index.php" class="row g-3 align-items-end">
                <input type="hidden" name="route" value="usuarios">

                <div class="col-12 col-md-5">
                    <label for="filtroBusca" class="form-label fw-semibold text-secondary small text-uppercase">Buscar por Nome ou E-mail</label>
                    <input type="text" class="form-control" id="filtroBusca" name="busca" placeholder="Ex: Maria, admin@email.com..." value="<?php echo htmlspecialchars($filtros['busca'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filtroPerfil" class="form-label fw-semibold text-secondary small text-uppercase">Perfil</label>
                    <select class="form-select" id="filtroPerfil" name="perfil">
                        <option value="">Todos os Perfis</option>
                        <option value="administrador" <?php echo ($filtros['perfil'] ?? '') === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="operador" <?php echo ($filtros['perfil'] ?? '') === 'operador' ? 'selected' : ''; ?>>Operador</option>
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
                    <a href="?route=usuarios" class="btn btn-outline-secondary" title="Limpar Filtros">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Usuários -->
    <div class="card border rounded-3 bg-white shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom" style="background-color: var(--color-surface-container-low) !important;">
                    <tr>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Nome / E-mail</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Perfil</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Status</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Auditoria</th>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-people text-muted mb-3 opacity-50" viewBox="0 0 16 16">
                                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.004-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.72C2.312 10.629 3.282 10 5 10q.423 0 .92.02M1.5 5.5a2.5 2.5 0 1 0 5 0 2.5 2.5 0 0 0-5 0m5-3a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0"/>
                                    </svg>
                                    <p class="h6 text-secondary mb-2">Nenhum usuário encontrado.</p>
                                    <p class="text-muted small mb-3">Tente ajustar os filtros de busca ou cadastre um novo usuário.</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="limparFormularioUsuario()">
                                        Cadastrar Usuário
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usr): ?>
                            <?php $eUsuarioLogado = ((int)$usr['id'] === (int)$usuarioLogado['id']); ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark">
                                        <?php echo htmlspecialchars($usr['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if ($eUsuarioLogado): ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle ms-1" style="font-size: 0.7rem;">Você</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($usr['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </td>
                                <td class="px-3 py-3">
                                    <?php if ($usr['perfil'] === 'administrador'): ?>
                                        <span class="badge bg-primary px-2 py-1 text-uppercase" style="background-color: var(--color-primary-container) !important; color: #ffffff !important; font-size: 0.7rem;">
                                            Administrador
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 text-uppercase" style="font-size: 0.7rem;">
                                            Operador
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-3">
                                    <?php echo FormatHelper::statusBadge($usr['status']); ?>
                                </td>
                                <td class="px-3 py-3 text-muted small">
                                    <div>Cadastrado por <strong><?php echo htmlspecialchars($usr['criado_por_nome'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?></strong> em <?php echo FormatHelper::dataHora($usr['criado_em']); ?></div>
                                    <?php if (!empty($usr['alterado_em'])): ?>
                                        <div class="text-secondary" style="font-size: 0.75rem;">Editado por <?php echo htmlspecialchars($usr['alterado_por_nome'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?> em <?php echo FormatHelper::dataHora($usr['alterado_em']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editarUsuario(<?php echo htmlspecialchars(json_encode($usr), ENT_QUOTES, 'UTF-8'); ?>)"
                                                title="Editar Usuário">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                            </svg>
                                        </button>

                                        <?php if ($eUsuarioLogado): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary opacity-50" disabled title="Você não pode inativar seu próprio usuário logado.">
                                                Inativar
                                            </button>
                                        <?php else: ?>
                                            <form method="POST" action="?route=usuarios_status" class="d-inline" onsubmit="return confirm('Deseja realmente <?php echo $usr['status'] === 'ativo' ? 'inativar' : 'ativar'; ?> este usuário?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int)$usr['id']; ?>">
                                                <input type="hidden" name="status" value="<?php echo $usr['status'] === 'ativo' ? 'inativo' : 'ativo'; ?>">
                                                
                                                <?php if ($usr['status'] === 'ativo'): ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Inativar Usuário">
                                                        Inativar
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Ativar Usuário">
                                                        Ativar
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        <?php endif; ?>
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

<!-- Modal de Cadastro/Edição de Usuário -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="background-color: var(--color-primary) !important;">
                <h5 class="modal-title fw-bold" id="modalUsuarioLabel">Novo Usuário</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="POST" action="?route=usuarios_salvar">
                <div class="modal-body p-4">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" id="usuarioId" value="">

                    <div class="mb-3">
                        <label for="usuarioNome" class="form-label fw-semibold">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="usuarioNome" name="nome" placeholder="Ex: Maria Silva" required minlength="3" maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label for="usuarioEmail" class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="usuarioEmail" name="email" placeholder="Ex: maria@empresa.com" required maxlength="255">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="usuarioPerfil" class="form-label fw-semibold">Perfil <span class="text-danger">*</span></label>
                            <select class="form-select" id="usuarioPerfil" name="perfil" required>
                                <option value="operador">Operador</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="usuarioStatus" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="usuarioStatus" name="status" required>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="usuarioSenha" class="form-label fw-semibold" id="usuarioSenhaLabel">Senha Inicial <span class="text-danger" id="usuarioSenhaRequired">*</span></label>
                        <input type="password" class="form-control" id="usuarioSenha" name="senha" placeholder="No mínimo 8 caracteres">
                        <div class="form-text text-muted" id="usuarioSenhaHelp">
                            Na criação, a senha é obrigatória. O usuário deverá alterá-la no primeiro acesso.
                        </div>
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
function limparFormularioUsuario() {
    document.getElementById('modalUsuarioLabel').textContent = 'Novo Usuário';
    document.getElementById('usuarioId').value = '';
    document.getElementById('usuarioNome').value = '';
    document.getElementById('usuarioEmail').value = '';
    document.getElementById('usuarioPerfil').value = 'operador';
    document.getElementById('usuarioStatus').value = 'ativo';
    document.getElementById('usuarioStatus').disabled = false;
    document.getElementById('usuarioPerfil').disabled = false;
    
    var senhaInput = document.getElementById('usuarioSenha');
    senhaInput.value = '';
    senhaInput.required = true;
    document.getElementById('usuarioSenhaLabel').textContent = 'Senha Inicial ';
    document.getElementById('usuarioSenhaRequired').style.display = 'inline';
    document.getElementById('usuarioSenhaHelp').textContent = 'A senha deve conter no mínimo 8 caracteres. O usuário será solicitado a trocá-la no primeiro acesso.';
}

function editarUsuario(usr) {
    document.getElementById('modalUsuarioLabel').textContent = 'Editar Usuário';
    document.getElementById('usuarioId').value = usr.id;
    document.getElementById('usuarioNome').value = usr.nome;
    document.getElementById('usuarioEmail').value = usr.email;
    document.getElementById('usuarioPerfil').value = usr.perfil;
    document.getElementById('usuarioStatus').value = usr.status;
    
    var eProprioUsuario = (parseInt(usr.id) === <?php echo (int)$usuarioLogado['id']; ?>);
    
    // Desabilita alteração de status/perfil se for a própria conta conectada
    var statusSelect = document.getElementById('usuarioStatus');
    var perfilSelect = document.getElementById('usuarioPerfil');
    if (eProprioUsuario) {
        statusSelect.value = 'ativo';
        statusSelect.disabled = true;
        perfilSelect.value = 'administrador';
        perfilSelect.disabled = true;
    } else {
        statusSelect.disabled = false;
        perfilSelect.disabled = false;
    }

    var senhaInput = document.getElementById('usuarioSenha');
    senhaInput.value = '';
    senhaInput.required = false;
    document.getElementById('usuarioSenhaLabel').textContent = 'Nova Senha (Opcional)';
    document.getElementById('usuarioSenhaRequired').style.display = 'none';
    document.getElementById('usuarioSenhaHelp').textContent = 'Preencha apenas se desejar redefinir a senha do usuário (mínimo 8 caracteres).';
    
    var modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
