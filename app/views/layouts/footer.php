<?php
/**
 * Finzy — Layout Rodapé da Aplicação (footer.php)
 * 
 * Rodapé principal com encerramento de HTML e scripts JS.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}
?>
    <footer class="footer mt-auto py-3 bg-white border-top text-center text-muted" style="font-size: 0.85rem;">
        <div class="container">
            <span class="text-muted">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?> — Sistema de Gestão Financeira. Todos os direitos reservados.</span>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Local -->
    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
    <!-- App JS Local -->
    <script src="assets/js/app.js"></script>
</body>
</html>
