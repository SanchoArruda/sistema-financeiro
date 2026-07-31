<?php
/**
 * Finzy — View de Auto-gestão de Perfil (app/views/usuarios/meu_perfil.php)
 * 
 * Permite que qualquer usuário autenticado altere seu próprio nome e senha.
 * Segue o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Meu Perfil — Finzy';
require __DIR__ . '/../layouts/header.php';
?>

<main class="container py-4 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            
            <!-- Cabeçalho -->
            <div class="mb-4 text-center text-md-start">
                <h1 class="h3 fw-bold text-dark mb-1">Meu Perfil</h1>
                <p class="text-muted mb-0">Atualize suas informações pessoais e credenciais de acesso.</p>
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
                    <strong>Sucesso!</strong> Dados do seu perfil atualizados com êxito.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <!-- Card de Formulário -->
            <div class="card border rounded-3 bg-white shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-primary text-white p-3 px-4" style="background-color: var(--color-primary) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-badge" viewBox="0 0 16 16">
                            <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/>
                        </svg>
                        <h2 class="h5 fw-bold mb-0">Informações da Conta</h2>
                    </div>
                </div>

                <form method="POST" action="?route=salvar_meu_perfil" class="p-4">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

                    <!-- E-mail (Leitura apenas) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small text-uppercase mb-1">E-mail de Acesso</label>
                        <input type="email" class="form-control bg-light text-muted" value="<?php echo htmlspecialchars($usuarioCompleto['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly disabled>
                        <div class="form-text text-muted" style="font-size: 0.8rem;">
                            O endereço de e-mail é utilizado como identificador de login e não pode ser alterado diretamente.
                        </div>
                    </div>

                    <!-- Perfil e Data (Leitura apenas) -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase mb-1">Perfil</label>
                            <div>
                                <?php if (($usuarioCompleto['perfil'] ?? '') === 'administrador'): ?>
                                    <span class="badge bg-primary px-3 py-2 text-uppercase" style="background-color: var(--color-primary-container) !important; font-size: 0.75rem;">
                                        Administrador
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 text-uppercase" style="font-size: 0.75rem;">
                                        Operador
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase mb-1">Membro Desde</label>
                            <div class="form-control-plaintext small text-dark fw-semibold">
                                <?php echo FormatHelper::dataHora($usuarioCompleto['criado_em'] ?? ''); ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Nome Completo -->
                    <div class="mb-3">
                        <label for="nome" class="form-label fw-semibold">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($usuarioCompleto['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required minlength="3" maxlength="150">
                    </div>

                    <hr class="my-4">

                    <!-- Alteração de Senha (Opcional) -->
                    <h3 class="h6 fw-bold text-dark mb-3">Alterar Senha de Acesso</h3>

                    <div class="mb-3">
                        <label for="nova_senha" class="form-label fw-semibold">Nova Senha</label>
                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" placeholder="Deixe em branco se não desejar alterar" minlength="8">
                        <div class="form-text text-muted" style="font-size: 0.8rem;">
                            Preencha apenas se desejar modificar sua senha (mínimo de 8 caracteres).
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="confirmar_nova_senha" class="form-label fw-semibold">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="confirmar_nova_senha" name="confirmar_nova_senha" placeholder="Repita a nova senha">
                    </div>

                    <hr class="my-4">

                    <!-- Senha Atual (Obrigatória para confirmar alterações) -->
                    <div class="mb-4 p-3 bg-light border rounded-3">
                        <label for="senha_atual" class="form-label fw-bold text-dark mb-1">Senha Atual <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual para autorizar as alterações" required>
                        <div class="form-text text-muted" style="font-size: 0.8rem;">
                            Por segurança, sua senha atual é necessária para salvar qualquer alteração de perfil.
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <a href="?route=dashboard" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
