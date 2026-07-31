<?php
/**
 * Finzy — View de Troca Obrigatória de Senha no Primeiro Acesso (app/views/auth/primeiro_acesso.php)
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Troca de Senha Obrigatória — ' . (defined('APP_NAME') ? APP_NAME : 'Finzy');
require __DIR__ . '/../layouts/auth_header.php';
?>

<main class="container max-w-md w-100" style="max-width: 440px;">
    <div class="card shadow-sm p-4 border rounded-3 bg-white">
        <!-- Cabeçalho -->
        <div class="text-center mb-4">
            <h1 class="h4 fw-bold text-primary mb-1" style="color: var(--color-primary);">
                Troca de Senha Obrigatória
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Por razões de segurança, altere a sua senha temporária de primeiro acesso.
            </p>
        </div>

        <!-- Alertas -->
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger py-2 px-3 mb-3 text-sm rounded-2" role="alert">
                <div style="font-size: 0.9rem;"><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Caixa de orientação de segurança -->
        <div class="alert alert-warning py-2 px-3 mb-3 rounded-2" style="font-size: 0.85rem; background-color: #fff8e1; border-color: #ffe082; color: #5d4037;">
            <strong>Atenção:</strong> Crie uma senha segura com no mínimo 6 caracteres.
        </div>

        <!-- Formulário -->
        <form action="?route=processar_primeiro_acesso" method="POST" autocomplete="off" novalidate>
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

            <!-- Campo Nova Senha -->
            <div class="mb-3">
                <label for="nova_senha" class="form-label fw-semibold text-dark" style="font-size: 0.9rem;">Nova Senha</label>
                <input type="password" 
                       class="form-control form-control-lg text-sm" 
                       id="nova_senha" 
                       name="nova_senha" 
                       placeholder="Digite a nova senha" 
                       required 
                       autofocus
                       style="font-size: 0.95rem;">
            </div>

            <!-- Campo Confirmação de Senha -->
            <div class="mb-4">
                <label for="confirmacao_senha" class="form-label fw-semibold text-dark" style="font-size: 0.9rem;">Confirmar Nova Senha</label>
                <input type="password" 
                       class="form-control form-control-lg text-sm" 
                       id="confirmacao_senha" 
                       name="confirmacao_senha" 
                       placeholder="Digite a nova senha novamente" 
                       required
                       style="font-size: 0.95rem;">
            </div>

            <!-- Botões de Ação -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-2 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                    Salvar Nova Senha e Continuar
                </button>
                <a href="?route=logout" class="btn btn-ghost text-muted py-2 text-center" style="font-size: 0.9rem; text-decoration: none;">
                    Sair do Sistema
                </a>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../layouts/auth_footer.php'; ?>
