<?php
/**
 * API para buscar todos os cadastros e fornecer em formato JSON para o DataTables.
 */

// Define o cabeçalho como JSON para a resposta
header('Content-Type: application/json; charset=utf-8');

// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/conexao.php';

try {
    // Prepara e executa a consulta SQL para buscar todos os cadastros
    // Ordenamos por ID de forma decrescente para mostrar os mais recentes primeiro
    $sql = "SELECT id, tipo, nome_razao, cnpj_cpf, endereco, email, celular, data_cadastro FROM cadastros_hair_2025 ORDER BY id DESC";
    $stmt = $pdo->query($sql);

    // Busca todos os resultados como um array associativo
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // É uma boa prática formatar os dados, como a data, antes de enviar
    $dados_formatados = [];
    foreach ($resultados as $row) {
        // Formata a data para o padrão brasileiro (DD/MM/AAAA HH:MM)
        $row['data_cadastro_fmt'] = date('d/m/Y H:i', strtotime($row['data_cadastro']));
        $dados_formatados[] = $row;
    }

    // O DataTables espera um objeto JSON com uma chave "data" contendo os registros
    echo json_encode(['data' => $dados_formatados]);

} catch (PDOException $e) {
    // Em caso de erro, retorna um JSON de erro
    http_response_code(500);
    // Em produção, logue o erro em vez de exibi-lo
    // error_log("Erro ao buscar cadastros: " . $e->getMessage());
    echo json_encode(['data' => [], 'error' => 'Não foi possível buscar os dados do banco.']);
}

?>