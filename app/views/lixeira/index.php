<?php
/**
 * Finzy — View da Lixeira (app/views/lixeira/index.php)
 * 
 * Exibe a listagem de lançamentos excluídos logicamente e permite a restauração
 * com base no perfil de acesso (Administrador / Operador).
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$csrfToken = $csrfToken ?? AuthHelper::generateCsrfToken();

require __DIR__ . '/../layouts/header.php';
?>

<main class="container-fluid py-4 px-4 flex-grow-1">
    <!-- Cabeçalho da Página -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between pb-3 mb-4 border-bottom gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-trash3 text-muted" viewBox="0 0 16 16">
                    <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                </svg>
                Lixeira
            </h1>
            <p class="text-muted mb-0 small">
                <?php if ($isAdmin): ?>
                    Exibindo todos os lançamentos excluídos logicamente do sistema. Administradores podem restaurar qualquer item.
                <?php else: ?>
                    Exibindo os lançamentos excluídos por você. Você pode restaurar qualquer item da sua lista.
                <?php endif; ?>
            </p>
        </div>
        <div>
            <a href="?route=lancamentos" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
                Voltar para Lançamentos
            </a>
        </div>
    </div>

    <!-- Mensagens de Alerta (Sucesso / Erro) -->
    <?php if (!empty($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill flex-shrink-0" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l5.192-5.754a.75.75 0 0 0-.022-1.08z"/>
            </svg>
            <div><?php echo $_SESSION['mensagem_sucesso']; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php unset($_SESSION['mensagem_sucesso']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['mensagem_erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <div><?php echo $_SESSION['mensagem_erro']; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
        <?php unset($_SESSION['mensagem_erro']); ?>
    <?php endif; ?>

    <!-- Formulário de Filtro e Busca -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="" class="row g-3 align-items-center">
                <input type="hidden" name="route" value="lixeira">

                <div class="col-12 col-md-8 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por descrição..." value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-primary px-4" style="background-color: var(--color-primary); border-color: var(--color-primary);">Buscar</button>
                    </div>
                </div>

                <?php if (!empty($busca)): ?>
                    <div class="col-auto">
                        <a href="?route=lixeira" class="btn btn-outline-secondary btn-sm">Limpar busca</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tabela ou Estado Vazio -->
    <?php if (empty($itens)): ?>
        <div class="card shadow-sm border text-center p-5 bg-white my-4">
            <div class="card-body">
                <div class="d-inline-flex align-items-center justify-content-center bg-light text-secondary rounded-circle mb-3" style="width: 72px; height: 72px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-trash-slash" viewBox="0 0 16 16">
                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5z"/>
                        <path d="M2.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0v-1a.5.5 0 0 1 .5-.5m10 0a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0v-1a.5.5 0 0 1 .5-.5M5 5.5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5m3 0a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5"/>
                    </svg>
                </div>
                <h2 class="h5 fw-bold text-dark mb-2">Nenhum item na lixeira</h2>
                <p class="text-muted max-w-md mx-auto mb-4" style="max-width: 480px;">
                    <?php if (!empty($busca)): ?>
                        Nenhum lançamento excluído corresponde aos critérios de busca "<strong><?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?></strong>".
                    <?php else: ?>
                        Não há lançamentos excluídos no momento. Quando um registro for removido, ele ficará disponível nesta área para consulta e restauração.
                    <?php endif; ?>
                </p>
                <div>
                    <a href="?route=lancamentos" class="btn btn-primary px-4 py-2" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                        Voltar para Lançamentos
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0 mb-4 bg-white overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        <tr>
                            <th scope="col" class="py-3 px-3">Data Exclusão</th>
                            <th scope="col" class="py-3 px-3">Excluído Por</th>
                            <th scope="col" class="py-3 px-3">Tipo</th>
                            <th scope="col" class="py-3 px-3">Descrição</th>
                            <th scope="col" class="py-3 px-3">Categoria / Conta</th>
                            <th scope="col" class="py-3 px-3 text-end">Valor</th>
                            <th scope="col" class="py-3 px-3 text-center">Data Lançamento</th>
                            <th scope="col" class="py-3 px-3 text-end">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td class="px-3 text-nowrap text-muted small">
                                    <?php echo FormatHelper::dataHora($item['excluido_em']); ?>
                                </td>
                                <td class="px-3 text-nowrap">
                                    <span class="fw-semibold text-dark"><?php echo htmlspecialchars($item['excluidor_nome'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-3 text-nowrap">
                                    <?php if ($item['tipo'] === 'receita'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Receita</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Despesa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3">
                                    <span class="fw-medium text-dark"><?php echo htmlspecialchars($item['descricao'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="px-3 small text-muted">
                                    <div><?php echo htmlspecialchars($item['categoria_nome'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="text-xs text-secondary opacity-75"><?php echo htmlspecialchars($item['conta_nome'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </td>
                                <td class="px-3 text-end text-nowrap fw-semibold <?php echo $item['tipo'] === 'receita' ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo FormatHelper::moeda($item['valor']); ?>
                                </td>
                                <td class="px-3 text-center text-nowrap small text-muted">
                                    <?php echo FormatHelper::data($item['data_lancamento']); ?>
                                </td>
                                <td class="px-3 text-end text-nowrap">
                                    <button type="button" 
                                            class="btn btn-outline-success btn-sm px-3 d-inline-flex align-items-center gap-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalRestaurar<?php echo $item['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-arrow-counterclockwise" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2z"/>
                                            <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466"/>
                                        </svg>
                                        Restaurar
                                    </button>

                                    <!-- Modal de Confirmação de Restauração -->
                                    <div class="modal fade text-start" id="modalRestaurar<?php echo $item['id']; ?>" tabindex="-1" aria-labelledby="modalRestaurarLabel<?php echo $item['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title h6 fw-bold" id="modalRestaurarLabel<?php echo $item['id']; ?>">Confirmar Restauração</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                </div>
                                                <div class="modal-body py-4">
                                                    <p class="mb-2">Deseja realmente restaurar este lançamento da lixeira?</p>
                                                    <div class="card bg-light border-0 p-3 mb-0">
                                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($item['descricao'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                        <div class="small text-muted mt-1">
                                                            Valor: <strong><?php echo FormatHelper::moeda($item['valor']); ?></strong> | 
                                                            Data: <?php echo FormatHelper::data($item['data_lancamento']); ?>
                                                        </div>
                                                    </div>
                                                    <p class="small text-muted mt-3 mb-0">
                                                        O item retornará para a listagem padrão de lançamentos e passará a afetar os saldos e relatórios novamente.
                                                    </p>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                    <form method="POST" action="?route=lixeira_restaurar" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                        <button type="submit" class="btn btn-success btn-sm px-4">
                                                            Confirmar Restauração
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if ($totalPaginas > 1): ?>
                <div class="card-footer bg-white border-top py-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                    <span class="small text-muted">
                        Mostrando página <strong><?php echo $pagina; ?></strong> de <strong><?php echo $totalPaginas; ?></strong> (Total: <?php echo $totalItens; ?> itens)
                    </span>
                    <nav aria-label="Navegação da lixeira">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?route=lixeira&pagina=<?php echo $pagina - 1; ?><?php echo !empty($busca) ? '&busca=' . urlencode($busca) : ''; ?>">Anterior</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                                    <a class="page-link" href="?route=lixeira&pagina=<?php echo $i; ?><?php echo !empty($busca) ? '&busca=' . urlencode($busca) : ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $pagina >= $totalPaginas ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?route=lixeira&pagina=<?php echo $pagina + 1; ?><?php echo !empty($busca) ? '&busca=' . urlencode($busca) : ''; ?>">Próxima</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
