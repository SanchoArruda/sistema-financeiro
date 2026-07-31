<?php
/**
 * Finzy — Layout Rodapé da Aplicação (footer.php)
 * 
 * Rodapé principal com encerramento do layout e scripts JS.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$usuarioLogado = AuthHelper::getLoggedUser();
?>
<?php if ($usuarioLogado): ?>
            </main>

            <!-- Rodapé Principal -->
            <footer class="footer mt-auto py-3 bg-white border-top text-center text-muted" style="font-size: 0.85rem;">
                <div class="container-fluid">
                    <span class="text-muted">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?> — Sistema de Gestão Financeira. Todos os direitos reservados.</span>
                </div>
            </footer>
        </div> <!-- Fim de .app-main -->
    </div> <!-- Fim de .app-wrapper -->
<?php else: ?>
    </main>
<?php endif; ?>

    <!-- Bootstrap 5 JS Local -->
    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
    <!-- App JS Local -->
    <script src="assets/js/app.js"></script>
</body>
</html>
