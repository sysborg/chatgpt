<?php

// Exemplos de uso do pacote Sysborg ChatGPT

use Sysborg\ChatGPT\Facades\ChatGPT;
use Sysborg\ChatGPT\Exceptions\ChatGPTException;
use Sysborg\ChatGPT\Exceptions\RateLimitException;

// Exemplo 1: Chat simples
function exemploChat()
{
    try {
        $response = ChatGPT::chat('Olá! Como você pode me ajudar hoje?');
        
        echo "Resposta: " . $response->getContent() . "\n";
        echo "Tokens utilizados: " . $response->getTotalTokens() . "\n";
        echo "Modelo usado: " . $response->getModel() . "\n";
        
    } catch (ChatGPTException $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
}

// Exemplo 2: Chat com histórico de conversa
function exemploChatComHistorico()
{
    $historico = [
        ['role' => 'system', 'content' => 'Você é um assistente especializado em Laravel.'],
        ['role' => 'user', 'content' => 'Como criar um middleware no Laravel?'],
        ['role' => 'assistant', 'content' => 'Para criar um middleware no Laravel, use o comando artisan: php artisan make:middleware NomeDoMiddleware'],
        ['role' => 'user', 'content' => 'E como registrar esse middleware?']
    ];
    
    try {
        $response = ChatGPT::chatWithHistory($historico);
        echo "Resposta contextual: " . $response->getContent() . "\n";
        
    } catch (ChatGPTException $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
}

// Exemplo 3: Configuração dinâmica
function exemploConfiguracaoDinamica()
{
    try {
        $response = ChatGPT::setModel('gpt-4')
            ->setTemperature(0.9)
            ->setMaxTokens(2000)
            ->chat('Conte uma história criativa sobre um robô que aprende a sentir emoções');
            
        echo "História criativa: " . $response->getContent() . "\n";
        echo "Criatividade (temperature): 0.9\n";
        
    } catch (ChatGPTException $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
}

// Exemplo 4: Análise de imagem
function exemploAnaliseImagem()
{
    try {
        // Exemplo com URL
        $imageUrl = 'https://example.com/minha-imagem.jpg';
        $response = ChatGPT::vision($imageUrl, 'Descreva esta imagem em detalhes', [
            'detail' => 'high'
        ]);
        
        echo "Análise da imagem: " . $response->getAnalysis() . "\n";
        echo "Resumo: " . $response->getSummary() . "\n";
        
        // Verificar entidades detectadas
        $entidades = $response->getDetectedEntities();
        if (!empty($entidades)) {
            echo "Entidades detectadas:\n";
            foreach ($entidades as $categoria => $items) {
                echo "  $categoria: " . implode(', ', $items) . "\n";
            }
        }
        
        // Verificar questões de segurança
        if ($response->hasSafetyConcerns()) {
            echo "ATENÇÃO: A imagem pode conter conteúdo inadequado\n";
        }
        
    } catch (ChatGPTException $e) {
        echo "Erro na análise: " . $e->getMessage() . "\n";
    }
}

// Exemplo 5: Análise de imagem com base64
function exemploImagemBase64()
{
    try {
        // Carregar imagem local
        $imagePath = '/path/to/local/image.jpg';
        
        if (file_exists($imagePath)) {
            $imageData = file_get_contents($imagePath);
            $base64Image = base64_encode($imageData);
            
            $response = ChatGPT::visionFromBase64(
                $base64Image,
                'Que tipo de objeto ou cena é mostrada nesta imagem? Liste as características principais.'
            );
            
            echo "Análise da imagem local: " . $response->getAnalysis() . "\n";
        } else {
            echo "Arquivo de imagem não encontrado: $imagePath\n";
        }
        
    } catch (ChatGPTException $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
}

// Exemplo 6: Text completion (legacy)
function exemploCompletion()
{
    try {
        $prompt = "Era uma vez, em um reino muito distante, uma princesa que";
        
        $response = ChatGPT::completion($prompt, [
            'model' => 'gpt-3.5-turbo-instruct',
            'max_tokens' => 150,
            'temperature' => 0.8
        ]);
        
        echo "História completada: " . $prompt . $response->getContent() . "\n";
        
    } catch (ChatGPTException $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
}

// Exemplo 7: Tratamento de rate limit
function exemploRateLimit()
{
    try {
        // Verificar status do rate limit
        $status = ChatGPT::getRateLimitStatus();
        echo "Rate Limit Status:\n";
        echo "  Limite: " . $status['limit'] . " requisições/minuto\n";
        echo "  Restantes: " . $status['remaining'] . "\n";
        echo "  Reset em: " . date('H:i:s', $status['reset_at']) . "\n";
        
        // Fazer várias requisições
        for ($i = 1; $i <= 3; $i++) {
            $response = ChatGPT::chat("Esta é a mensagem número $i");
            echo "Resposta $i: " . substr($response->getContent(), 0, 50) . "...\n";
        }
        
    } catch (RateLimitException $e) {
        echo "Rate limit excedido!\n";
        echo "Tente novamente em: " . $e->getRetryAfter() . " segundos\n";
    } catch (ChatGPTException $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
}

// Exemplo 8: Análise detalhada de resposta
function exemploAnaliseResposta()
{
    try {
        $response = ChatGPT::chat('Explique brevemente o que é inteligência artificial');
        
        echo "=== ANÁLISE DETALHADA DA RESPOSTA ===\n";
        echo "ID: " . $response->getId() . "\n";
        echo "Modelo: " . $response->getModel() . "\n";
        echo "Criado em: " . date('Y-m-d H:i:s', $response->getCreated()) . "\n";
        echo "Role: " . $response->getRole() . "\n";
        echo "Motivo da finalização: " . $response->getFinishReason() . "\n";
        
        echo "\nTOKENS:\n";
        echo "  Prompt: " . $response->getPromptTokens() . "\n";
        echo "  Completion: " . $response->getCompletionTokens() . "\n";
        echo "  Total: " . $response->getTotalTokens() . "\n";
        
        echo "\nSTATUS:\n";
        echo "  Completa: " . ($response->isComplete() ? 'Sim' : 'Não') . "\n";
        echo "  Truncada: " . ($response->isTruncated() ? 'Sim' : 'Não') . "\n";
        echo "  Filtrada: " . ($response->isFiltered() ? 'Sim' : 'Não') . "\n";
        
        echo "\nCONTEÚDO:\n";
        echo $response->getContent() . "\n";
        
        // Converter para array
        echo "\n=== ARRAY COMPLETO ===\n";
        print_r($response->toArray());
        
    } catch (ChatGPTException $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
}

// Exemplo 9: Múltiplos modelos
function exemploMultiplosModelos()
{
    $modelos = ChatGPT::getAvailableModels();
    $pergunta = 'Qual é a diferença entre PHP e JavaScript?';
    
    foreach (['gpt-3.5-turbo', 'gpt-4'] as $modelo) {
        if (in_array($modelo, $modelos)) {
            try {
                echo "\n=== RESPOSTA DO $modelo ===\n";
                
                $response = ChatGPT::setModel($modelo)->chat($pergunta);
                echo $response->getContent() . "\n";
                echo "Tokens: " . $response->getTotalTokens() . "\n";
                
            } catch (ChatGPTException $e) {
                echo "Erro com $modelo: " . $e->getMessage() . "\n";
            }
        }
    }
}

// Executar exemplos
echo "=== EXEMPLOS DE USO DO PACOTE SYSBORG CHATGPT ===\n\n";

exemploChat();
echo "\n" . str_repeat("-", 50) . "\n";

exemploChatComHistorico();
echo "\n" . str_repeat("-", 50) . "\n";

exemploConfiguracaoDinamica();
echo "\n" . str_repeat("-", 50) . "\n";

// Descomente para testar análise de imagens
// exemploAnaliseImagem();
// exemploImagemBase64();

exemploCompletion();
echo "\n" . str_repeat("-", 50) . "\n";

exemploRateLimit();
echo "\n" . str_repeat("-", 50) . "\n";

exemploAnaliseResposta();
echo "\n" . str_repeat("-", 50) . "\n";

exemploMultiplosModelos();