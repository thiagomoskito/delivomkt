<?php
// Adicione estas 3 linhas no topo para depuração durante o desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Usa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Define que a resposta será em formato JSON
header('Content-Type: application/json');

// --- 1. Conexão com o Banco de Dados ---
require_once __DIR__ . '/conexao.php';

// Verifica se o método da requisição é POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // --- COLETA E LIMPEZA DOS DADOS ---
  $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);
  $nome_razao = filter_input(INPUT_POST, 'nome-razao', FILTER_SANITIZE_SPECIAL_CHARS);
  $cnpj_cpf = filter_input(INPUT_POST, 'cnpj-cpf', FILTER_SANITIZE_SPECIAL_CHARS);
  $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_SPECIAL_CHARS);
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  $celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_SPECIAL_CHARS);

  // --- VALIDAÇÃO ---
  if (empty($tipo) || empty($nome_razao) || empty($cnpj_cpf) || empty($email)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos obrigatórios.']);
    exit;
  }

  // --- 2. Armazenamento no Banco de Dados (Mantido como está) ---
  try {
    $sql = "INSERT INTO cadastros_hair_2025 (tipo, nome_razao, cnpj_cpf, endereco, email, celular, data_cadastro) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tipo, $nome_razao, $cnpj_cpf, $endereco, $email, $celular, date('Y-m-d H:i:s')]);
  } catch (PDOException $e) {
    http_response_code(500);
    // Em produção, registre o erro em um log.
    error_log('Erro ao salvar no banco: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao processar seu cadastro (DB).']);
    exit;
  }

  // --- 3. LÓGICA DE ENVIO DE E-MAILS COM PHPMailer ---
  $mail = new PHPMailer(true);

  //$mail->SMTPDebug = 2; // O valor 2 mostra a conversa completa.

  try {
    // --- A. Configuração do Servidor SMTP ---
    $mail->isSMTP();
    $mail->Host       = 'mail.delivomkt.com.br';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contato@delivomkt.com.br';
    $mail->Password   = '!@#Delivo45*-+';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    // --- B. Lógica de Destinatários ---
    // Remetente
    $mail->setFrom('contato@delivomkt.com.br', 'Delivo MKT');

    // Destinatário Fixo (Sempre recebe)
    $mail->addAddress('thiago.moskito@delivo.com.br', 'Moskito (Admin)');

    // Destinatários Condicionais
    if ($tipo === 'Salão de beleza' || $tipo === 'Profissional autônomo') {
      $mail->addAddress('daniel.gontijo@delivo.com.br', 'Daniel Gontijo');
      $mail->addAddress('denilson.calais@delivo.com.br', 'Denilson Calais');
    } elseif ($tipo === 'Distribuidor') {
      $mail->addAddress('v.mestrinho@delivo.com.br', 'V. Mestrinho');
      $mail->addAddress('everson.mattos@delivo.com.br', 'Everson Mattos');
    }

    // E-mail para resposta
    $mail->addReplyTo($email, $nome_razao);

    // --- C. Conteúdo do E-mail para a Equipe ---
    $mail->isHTML(true);
    $mail->Subject = 'Novo Cadastro Recebido - ' . $nome_razao;
    $mail->Body    = "
        <html lang='pt-BR'>
        <body style='background-color:#212121; color:#ffffff; padding:16px; font-family: sans-serif;'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr><td align='center'>
                    <table width='600' border='0' cellspacing='0' cellpadding='0'>
                        <tr><td align='center' style='padding: 20px 0;'>
                            <img src='http://delivomkt.com.br/images/logo.png' alt='Delivo.com.br, de longe, a mais próxima de você!' style='display: block;'>
                        </td></tr>
                        <tr><td style='padding: 20px;'>
                            <h2>Novo cadastro recebido pelo site:</h2>
                        </td></tr>
                        <tr><td style='padding: 3px;'>
                            <p><strong>Data:</strong> " . date("d/m/Y H:i:s") . "</p>
                        </td></tr>
                        <tr><td style='padding: 3px;'>
                            <p><strong>Tipo:</strong> {$tipo}</p>
                        </td></tr>
                        <tr><td style='padding: 3px;'>
                            <p><strong>Nome/Razão Social:</strong> {$nome_razao}</p>
                        </td></tr>
                        <tr><td style='padding: 3px;'>
                            <p><strong>CNPJ/CPF:</strong> {$cnpj_cpf}</p>
                        </td></tr>
                        <tr><td style='padding: 3px;'>
                            <p><strong>Endereço:</strong> {$endereco}</p>
                        </td></tr>
                        <tr><td style='padding: 3px;'>
                            <p><strong>E-mail:</strong> {$email}</p>
                        </td></tr>
                        <tr><td style='padding: 3px;'>
                            <p><strong>Celular:</strong> {$celular}</p>
                        </td></tr>
                    </table>
                </td></tr>
            </table>
        </body></html>
        ";
    $mail->AltBody = "Novo cadastro de {$nome_razao}. Email: {$email}";

    // Envia o e-mail para a equipe
    $mail->send();

    // --- D. Envio do E-mail de Confirmação para o Usuário ---
    // Limpa os destinatários anteriores para enviar apenas para o usuário
    $mail->clearAddresses();
    $mail->clearReplyTos();

    // Adiciona o usuário como destinatário
    $mail->addAddress($email, $nome_razao);

    // Configura o conteúdo do e-mail
    $mail->Subject = 'Obrigado pelo seu cadastro!';
    $mail->Body    = "
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
    "; // Mantive abreviado para clareza

    // Envia o e-mail de confirmação
    $mail->send();

    // --- RESPOSTA DE SUCESSO PARA O JAVASCRIPT ---
    echo json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso!']);
  } catch (Exception $e) {
    http_response_code(500);
    // Em produção, registre o erro em um log.
    error_log("Erro no envio de e-mail: {$mail->ErrorInfo}");
    echo json_encode(['status' => 'error', 'message' => 'O cadastro foi salvo, mas houve um erro ao enviar o e-mail.']);
  }
} else {
  http_response_code(405);
  echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido.']);
}
