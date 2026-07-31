<?php
/**
 * Finzy — Helper de Envio de E-mail (MailHelper)
 * 
 * Responsável por formatar e enviar e-mails do sistema (ex: recuperação de senha).
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

class MailHelper {

    /**
     * Envia o e-mail de recuperação de senha com o link temporário contendo o token.
     * 
     * @param string $emailDestino E-mail do usuário
     * @param string $nomeUsuario Nome do usuário
     * @param string $token Token de redefinição
     * @return bool Retorna true se o processo de envio foi iniciado
     */
    public static function enviarEmailRecuperacao(string $emailDestino, string $nomeUsuario, string $token): bool {
        $baseUrl = defined('APP_URL') ? APP_URL : 'http://localhost/sistema_financeiro';
        $linkRedefinicao = $baseUrl . '/?route=redefinir_senha&token=' . urlencode($token);

        $assunto = 'Finzy — Recuperação de Senha';

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Finzy') . " <" . (defined('SMTP_FROM') ? SMTP_FROM : 'noreply@finzy.local') . ">\r\n";
        $headers .= "Reply-To: " . (defined('SMTP_FROM') ? SMTP_FROM : 'noreply@finzy.local') . "\r\n";

        $mensagemHtml = "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>Recuperação de Senha — Finzy</title>
        </head>
        <body style='font-family: Arial, sans-serif; background-color: #faf9fc; color: #1a1c1e; padding: 20px;'>
            <div style='max-width: 500px; margin: 0 auto; background: #ffffff; border: 1px solid #e3e2e6; border-radius: 8px; padding: 24px;'>
                <h2 style='color: #022448; margin-top: 0;'>Finzy - Gestão Financeira</h2>
                <p>Olá, <strong>" . htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8') . "</strong>!</p>
                <p>Recebemos uma solicitação para redefinir a sua senha de acesso ao sistema Finzy.</p>
                <p>Para criar uma nova senha, clique no botão abaixo ou copie e cole o link no seu navegador:</p>
                <div style='text-align: center; margin: 24px 0;'>
                    <a href='" . htmlspecialchars($linkRedefinicao, ENT_QUOTES, 'UTF-8') . "' 
                       style='background-color: #022448; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; display: inline-block;'>
                        Redefinir Minha Senha
                    </a>
                </div>
                <p style='font-size: 0.9em; color: #43474e;'>Este link é válido por <strong>24 horas</strong>. Se você não solicitou a redefinição de senha, desconsidere este e-mail.</p>
                <hr style='border: none; border-top: 1px solid #e3e2e6; margin: 20px 0;'>
                <p style='font-size: 0.8em; color: #74777f; text-align: center;'>Finzy — Controle Financeiro Simples e Colaborativo</p>
            </div>
        </body>
        </html>
        ";

        // Tenta enviar o e-mail via mail() do PHP (silenciado para evitar warnings em ambientes de desenvolvimento sem servidor SMTP configurado)
        @mail($emailDestino, $assunto, $mensagemHtml, $headers);

        return true;
    }
}
