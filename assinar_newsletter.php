<?php

// Define que a resposta será em formato JSON
header('Content-Type: application/json');

// --- 1. Conexão com o Banco de Dados ---
require_once __DIR__ . '/conexao.php'; // Usa o mesmo arquivo de conexão

// --- REMOVIDO: Configuração do arquivo TXT ---
// $arquivo_newsletter = "dados/cadastro_newsletter.txt";

// Garante que a requisição seja do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- VALIDAÇÃO DOS DADOS (Mantido) ---
    $aceite = filter_input(INPUT_POST, 'aceite', FILTER_SANITIZE_SPECIAL_CHARS);
    if ($aceite !== 'sim') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'É necessário aceitar os termos de privacidade.']);
        exit;
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Por favor, insira um endereço de e-mail válido.']);
        exit;
    }

    // --- 2. PROCESSAMENTO COM BANCO DE DADOS ---
    try {
        // --- Verificação de e-mail duplicado no banco ---
        $sql_check = "SELECT COUNT(*) FROM newsletter_inscritos WHERE email = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$email]);
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            // Se o e-mail já existe, retorna uma mensagem amigável.
            echo json_encode(['status' => 'success', 'message' => 'Seu e-mail já está em nossa lista. Obrigado!']);
            exit;
        }

        // --- Inserção do novo e-mail no banco ---
        // Se o e-mail não é duplicado, insere o novo registro.
        $sql_insert = "INSERT INTO newsletter_inscritos (email, data_inscricao) VALUES (?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);

        $stmt_insert->execute([
            $email,
            date('Y-m-d H:i:s') // Adiciona a data e hora atual da inscrição
        ]);

        // Retorna a mensagem de sucesso.
        echo json_encode(['status' => 'success', 'message' => 'Obrigado por assinar! Seu e-mail foi cadastrado.']);
    } catch (PDOException $e) {
        // Em caso de erro no banco, retorna uma mensagem de erro genérica.
        http_response_code(500); // Erro Interno do Servidor
        // Em produção, registre o erro em vez de exibi-lo.
        // error_log('Erro na newsletter: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro no servidor ao processar sua assinatura.']);
        exit;
    }
} else {
    // Retorna erro se o método não for POST
    http_response_code(405); // Método não permitido
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição não permitido.']);
}
