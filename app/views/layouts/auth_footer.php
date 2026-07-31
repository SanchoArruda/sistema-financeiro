<?php
/**
 * Finzy — Layout Rodapé de Autenticação (auth_footer.php)
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}
?>
    <footer class="mt-4 text-center text-muted font-size-sm" style="font-size: 0.85rem;">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?> — Sistema de Gestão Financeira</p>
    </footer>

    <!-- Bootstrap 5 JS Local -->
    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
    <!-- App JS Local -->
    <script src="assets/js/app.js"></script>
</body>
</html>
