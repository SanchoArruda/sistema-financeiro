<?php
/**
 * Finzy — View de Login (app/views/auth/login.php)
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Entrar — ' . (defined('APP_NAME') ? APP_NAME : 'Finzy');
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
                Gestão Financeira Colaborativa
            </p>
        </div>

        <!-- Mensagens de Alerta (Erro ou Sucesso) -->
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-3 text-sm rounded-2" role="alert">
                <div><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-3 text-sm rounded-2" role="alert">
                <div><?php echo htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logged_out']) && $_GET['logged_out'] === '1'): ?>
            <div class="alert alert-info d-flex align-items-center py-2 px-3 mb-3 text-sm rounded-2" role="alert">
                <div>Sessão encerrada com sucesso.</div>
            </div>
        <?php endif; ?>

        <!-- Formulário de Login -->
        <form action="?route=processar_login" method="POST" autocomplete="on" novalidate>
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

            <!-- Campo E-mail -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-dark" style="font-size: 0.9rem;">Endereço de E-mail</label>
                <input type="email" 
                       class="form-control form-control-lg text-sm" 
                       id="email" 
                       name="email" 
                       placeholder="seu.email@exemplo.com" 
                       required 
                       autofocus
                       style="font-size: 0.95rem;">
            </div>

            <!-- Campo Senha -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="senha" class="form-label fw-semibold text-dark mb-0" style="font-size: 0.9rem;">Senha de Acesso</label>
                    <a href="?route=esqueci_senha" class="text-decoration-none text-muted" style="font-size: 0.85rem;">Esqueceu a senha?</a>
                </div>
                <input type="password" 
                       class="form-control form-control-lg text-sm" 
                       id="senha" 
                       name="senha" 
                       placeholder="Sua senha" 
                       required
                       style="font-size: 0.95rem;">
            </div>

            <!-- Botão Entrar -->
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                Entrar no Sistema
            </button>
        </form>
    </div>
</main>

<?php require __DIR__ . '/../layouts/auth_footer.php'; ?>
