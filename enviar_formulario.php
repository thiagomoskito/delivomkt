<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define que a resposta será em formato JSON
header('Content-Type: application/json');

// --- 1. ADICIONADO: Conexão com o Banco de Dados ---
require_once __DIR__ . '/conexao.php';

// --- CONFIGURAÇÕES DE E-MAIL ---
$destinatario = "thiago.moskito@delivomkt.com.br";

// Verifica se o método da requisição é POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- COLETA E LIMPEZA DOS DADOS (Mantido) ---
    $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
    $nome_razao = filter_input(INPUT_POST, 'nome-razao', FILTER_SANITIZE_SPECIAL_CHARS);
    $cnpj_cpf = filter_input(INPUT_POST, 'cnpj-cpf', FILTER_SANITIZE_SPECIAL_CHARS);
    $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_SPECIAL_CHARS);

    // --- VALIDAÇÃO (Melhorada) ---
    if (empty($tipo) || empty($nome_razao) || empty($cnpj_cpf) || empty($email)) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos obrigatórios.']);
        exit;
    }

    // --- 2. Armazenamento no Banco de Dados ---
    try {
        // Prepara a instrução SQL para evitar injeção de SQL
        $sql = "INSERT INTO cadastros_hair_2025 (tipo, nome_razao, cnpj_cpf, endereco, email, celular, data_cadastro) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);

        // Executa a instrução, passando os valores de forma segura
        $stmt->execute([
            $tipo,
            $nome_razao,
            $cnpj_cpf,
            $endereco,
            $email,
            $celular,
            date('Y-m-d H:i:s') // Adiciona a data e hora atual
        ]);

    } catch (PDOException $e) {
        // Em caso de erro, retorna uma mensagem de erro genérica.
        http_response_code(500); // Erro Interno do Servidor
        // Em produção, você deve registrar o erro em um arquivo de log.
        // error_log('Erro ao salvar no banco: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao processar seu cadastro.']);
        exit;
    }

    // --- 3. ENVIO DE E-MAILS ---
    // Esta parte só executa se a inserção no banco for bem-sucedida.

    $assunto_admin = "Novo Cadastro Recebido - " . $nome_razao;
    $corpo_email_admin = "
        <html><body>
            <h2>Novo cadastro recebido pelo site:</h2>
            <p><strong>Data:</strong> " . date("d/m/Y H:i:s") . "</p>
            <p><strong>Tipo:</strong> {$tipo}</p>
            <p><strong>Nome/Razão Social:</strong> {$nome_razao}</p>
            <p><strong>CNPJ/CPF:</strong> {$cnpj_cpf}</p>
            <p><strong>Endereço:</strong> {$endereco}</p>
            <p><strong>E-mail:</strong> {$email}</p>
            <p><strong>Celular:</strong> {$celular}</p>
        </body></html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: contato@delivomkt.com.br" . "\r\n";
    $headers .= "Reply-To: {$email}" . "\r\n";

    mail($destinatario, $assunto_admin, $corpo_email_admin, $headers);

    $assunto_usuario = "Obrigado pelo seu cadastro!";
    $corpo_email_usuario = "
        <html lang='pt-BR'>
        <body style='background-color:#212121; color:#ffffff; padding:16px; font-family: sans-serif;'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr><td align='center'>
                    <table width='600' border='0' cellspacing='0' cellpadding='0'>
                        <tr><td align='center' style='padding: 20px 0;'>
                            <img src='http://delivomkt.com.br/images/logo.png' alt='Delivo, de longe, a mais próxima de você!' style='display: block;'>
                        </td></tr>
                        <tr><td style='padding: 20px;'>
                            <h2 style='color: #ffffff;'>Olá, {$nome_razao}!</h2>
                            <p style='color: #dddddd;'>Recebemos seu cadastro com sucesso.</p>
                            <p style='color: #dddddd;'>Em breve nossa equipe entrará em contato.</p>
                            <p style='color: #dddddd;'>Obrigado!</p>
                        </td></tr>
                    </table>
                </td></tr>
            </table>
        </body></html>
    ";

    mail($email, $assunto_usuario, $corpo_email_usuario, $headers);

    // --- RESPOSTA DE SUCESSO PARA O JAVASCRIPT ---
    echo json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso!']);

} else {
    // Se a requisição não for POST, retorna um erro.
    http_response_code(405); // Método não permitido
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido.']);
}