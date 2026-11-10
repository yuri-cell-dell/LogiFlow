<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect(string $url): never {
    header("Location: {$url}");
    exit;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sent = $_POST['csrf_token'] ?? '';
        if (!hash_equals(csrf_token(), $sent)) {
            http_response_code(403);
            exit('Solicitação inválida. Atualize a página e tente novamente.');
        }
    }
}
function flash(string $message, string $type = 'success'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}
function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert alert-' . e($f['type']) . ' alert-dismissible fade show" role="alert">'
            . e($f['message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['flash']);
    }
}
