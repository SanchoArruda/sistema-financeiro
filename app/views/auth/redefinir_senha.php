<?php
/**
 * Finzy — View de Redefinição de Senha via Token (app/views/auth/redefinir_senha.php)
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Criar Nova Senha — ' . (defined('APP_NAME') ? APP_NAME : 'Finzy');
require __DIR__ . '/../layouts/auth_header.php';
?>

<main class="container max-w-md w-100" style="max-width: 420px;">
    <div class="card shadow-sm p-4 border rounded-3 bg-white">
        <!-- Cabeçalho do Card com Marca -->
        <div class="text-center mb-4">
            <h1 class="h3 fw-bold text-primary mb-1" style="color: var(--color-primary);">
                <?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            <p class="text-secondary-subtle text-muted mb-0" style="font-size: 0.95rem;">
                Redefinição de Senha
            </p>
        </div>

        <!-- Mensagens de Alerta -->
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-3 text-sm rounded-2" role="alert">
                <div><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endif; ?>

        <p class="text-muted text-sm mb-3" style="font-size: 0.9rem;">
            Digite sua nova senha de acesso abaixo. A senha deve possuir no mínimo 6 caracteres.
        </p>

        <!-- Formulário de Nova Senha -->
        <form action="?route=processar_redefinir_senha" method="POST" autocomplete="off" novalidate>
            <!-- Token CSRF e Token de Recuperação -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">

            <!-- Campo Nova Senha -->
            <div class="mb-3">
                <label for="nova_senha" class="form-label fw-semibold text-dark" style="font-size: 0.9rem;">Nova Senha</label>
                <input type="password" 
                       class="form-control form-control-lg text-sm" 
                       id="nova_senha" 
                       name="nova_senha" 
                       placeholder="Mínimo 6 caracteres" 
                       required 
                       autofocus
                       style="font-size: 0.95rem;">
            </div>

            <!-- Campo Confirmação da Nova Senha -->
            <div class="mb-4">
                <label for="confirmacao_senha" class="form-label fw-semibold text-dark" style="font-size: 0.9rem;">Confirmar Nova Senha</label>
                <input type="password" 
                       class="form-control form-control-lg text-sm" 
                       id="confirmacao_senha" 
                       name="confirmacao_senha" 
                       placeholder="Repita a nova senha" 
                       required
                       style="font-size: 0.95rem;">
            </div>

            <!-- Botão Salvar Nova Senha -->
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                Salvar Nova Senha
            </button>

            <!-- Link de Retorno ao Login -->
            <div class="text-center">
                <a href="?route=login" class="text-decoration-none text-muted" style="font-size: 0.85rem;">
                    &larr; Cancelar e voltar ao Login
                </a>
            </div>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../layouts/auth_footer.php'; ?>
