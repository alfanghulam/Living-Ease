<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chatbot AI (OpenAI-compatible)
    |--------------------------------------------------------------------------
    | Isi CHATBOT_API_KEY di .env. Base URL default DeepSeek; bisa diganti
    | ke OpenAI, Groq, OpenRouter, Ollama, dsb selama endpoint-nya
    | POST {base_url}/chat/completions.
    */

    'key' => env('CHATBOT_API_KEY'),
    'base_url' => env('CHATBOT_BASE_URL', 'https://api.deepseek.com/v1'),
    'model' => env('CHATBOT_MODEL', 'deepseek-chat'),

    'max_tokens' => (int) env('CHATBOT_MAX_TOKENS', 400),
    'temperature' => (float) env('CHATBOT_TEMPERATURE', 0.6),
    'timeout' => (int) env('CHATBOT_TIMEOUT', 25),
    'history_limit' => (int) env('CHATBOT_HISTORY_LIMIT', 10),

    'store_name' => env('CHATBOT_STORE_NAME', 'LivingEase'),
    'whatsapp' => env('CHATBOT_WHATSAPP', '6281234567890'),

    'catalog_ttl' => (int) env('CHATBOT_CATALOG_TTL', 600),
];
