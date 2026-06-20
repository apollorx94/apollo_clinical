<?php

// ============================================================
// BLOCO 1 — FUNÇÃO QUE LÊ O ARQUIVO .env
// ============================================================
// Esta função recebe um caminho de arquivo como parâmetro,
// lê cada linha, separa em chave/valor e guarda em $_ENV.
// $_ENV é uma variável global do PHP para variáveis de ambiente.

function carregarEnv($caminho) {

    // lê o arquivo inteiro e devolve um array — cada item é uma linha
    // ex: ["DB_HOST=localhost\n", "DB_PORT=5432\n", ...]
    $linhas = file($caminho);

    // caractere que separa chave do valor em cada linha
    $delimitador = '=';

    foreach ($linhas as $linha) {

        // separa a linha em duas partes usando o "=" como ponto de corte
        // ex: "DB_HOST=localhost\n" vira ["DB_HOST", "localhost\n"]
        $arrayEnv = explode($delimitador, $linha);

        // trim() remove espaços e quebras de linha (\n) dos dois lados
        // $arrayEnv[0] = chave  → ex: "DB_HOST"
        // $arrayEnv[1] = valor  → ex: "localhost"
        // a chave dinâmica $_ENV[$arrayEnv[0]] usa o CONTEÚDO da variável como nome
        $_ENV[trim($arrayEnv[0])] = trim($arrayEnv[1]);
    }
}


// ============================================================
// BLOCO 2 — EXECUTA A FUNÇÃO
// ============================================================
// __DIR__ retorna o diretório onde este arquivo está salvo.
// Concatenado com '/.env', forma o caminho completo do arquivo.
// Esse caminho é passado pra função como o parâmetro $caminho.

carregarEnv(__DIR__ . '/.env');


// ============================================================
// BLOCO 3 — EXTRAI OS VALORES DO $_ENV PARA VARIÁVEIS LEGÍVEIS
// ============================================================
// Após a função rodar, $_ENV está populado com as chaves do .env.
// Aqui extraímos cada valor para variáveis com nomes mais claros,
// que serão usadas na string de conexão do PDO abaixo.

$dbHost  = $_ENV['DB_HOST'];   // endereço do servidor do banco
$dbPorta = $_ENV['DB_PORT'];   // porta (PostgreSQL padrão: 5432)
$dbName  = $_ENV['DB_NAME'];   // nome do banco de dados
$dbUser  = $_ENV['DB_USER'];   // usuário do banco
$dbPass  = $_ENV['DB_PASS'];   // senha do banco


// ============================================================
// BLOCO 4 — CONEXÃO COM O BANCO VIA PDO
// ============================================================
// PDO (PHP Data Objects) é a forma moderna de conectar ao banco.
// O try/catch garante que erros de conexão sejam tratados
// sem expor detalhes técnicos para o usuário final.

try {
    $conexao = new PDO(
        // string de conexão: define o driver (pgsql), host, porta e banco
        "pgsql:host=$dbHost;port=$dbPorta;dbname=$dbName",
        $dbUser,  // usuário
        $dbPass   // senha
    );

    // define que erros do PDO lançam exceções (necessário pro catch funcionar)
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Conexão bem sucedida!";

} catch (PDOException $erro) {
    // registra o erro técnico no arquivo de log (invisível pro usuário)
    error_log('Erro de conexão: ' . $erro->getMessage());

    // exibe mensagem genérica e encerra a execução
    die('Erro ao conectar ao banco de dados. Contate o administrador.');
}