<?php
define('DB_HOST', 'localhost'); 
define('DB_NAME', 'delivomkt_hair2025');    
define('DB_USER', 'delivomkt_hair2025'); 
define('DB_PASS', '!@#Senha45*-+'); 

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
        PDO::ATTR_EMULATE_PREPARES   => false,                  
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    http_response_code(500);
    die("Erro ao conectar ao banco de dados. Por favor, contate o administrador do site.");
}

?>