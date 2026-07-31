<?php
/**
 * Finzy — Helper de Formatação (FormatHelper)
 * 
 * Funções utilitárias reutilizáveis para formatação de moeda, datas e sanitização de valores.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

class FormatHelper {

    /**
     * Formata um valor numérico para o padrão de moeda brasileira (R$ 1.250,00).
     * 
     * @param float|int|string|null $valor
     * @return string
     */
    public static function moeda(float|int|string|null $valor): string {
        if ($valor === null || $valor === '') {
            $valor = 0;
        }
        $num = (float) $valor;
        return 'R$ ' . number_format($num, 2, ',', '.');
    }

    /**
     * Alias para moeda()
     */
    public static function formatarMoeda(float|int|string|null $valor): string {
        return self::moeda($valor);
    }

    /**
     * Converte um valor formatado (ex: "1.250,50" ou "1250.50") em float limpo para banco de dados.
     * 
     * @param string|float|int|null $valor
     * @return float
     */
    public static function parseMoeda(string|float|int|null $valor): float {
        if ($valor === null || $valor === '') {
            return 0.0;
        }

        if (is_float($valor) || is_int($valor)) {
            return (float) $valor;
        }

        $str = trim((string) $valor);
        // Remove símbolos de moeda R$ e espaços
        $str = preg_replace('/[R$\s]/u', '', $str);

        // Se contiver vírgula como separador decimal
        if (str_contains($str, ',')) {
            // Remove pontos de milhar
            $str = str_replace('.', '', $str);
            // Substitui vírgula por ponto
            $str = str_replace(',', '.', $str);
        }

        return (float) $str;
    }

    /**
     * Formata data no formato brasileiro (DD/MM/AAAA).
     * 
     * @param string|null $data String YYYY-MM-DD ou YYYY-MM-DD HH:ii:ss
     * @return string
     */
    public static function data(?string $data): string {
        if (empty($data)) {
            return '-';
        }
        $timestamp = strtotime($data);
        if (!$timestamp) {
            return '-';
        }
        return date('d/m/Y', $timestamp);
    }

    /**
     * Alias para data()
     */
    public static function formatarData(?string $data): string {
        return self::data($data);
    }

    /**
     * Formata data e hora no formato brasileiro (DD/MM/AAAA HH:mm).
     * 
     * @param string|null $dataHora
     * @return string
     */
    public static function dataHora(?string $dataHora): string {
        if (empty($dataHora)) {
            return '-';
        }
        $timestamp = strtotime($dataHora);
        if (!$timestamp) {
            return '-';
        }
        return date('d/m/Y H:i', $timestamp);
    }

    /**
     * Alias para dataHora()
     */
    public static function formatarDataHora(?string $dataHora): string {
        return self::dataHora($dataHora);
    }

    /**
     * Retorna a badge de status (Ativo / Inativo) formatada em HTML Bootstrap.
     * 
     * @param string $status 'ativo' ou 'inativo'
     * @return string
     */
    public static function statusBadge(string $status): string {
        if (strtolower($status) === 'ativo') {
            return '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Ativo</span>';
        }
        return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill">Inativo</span>';
    }
}
