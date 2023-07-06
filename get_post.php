<?php

require_once('wp-load.php');

$teams_api_key = "your-api-key-here";

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Authenticate POST request
    $payload = file_get_contents('php://input');
    $auth =   $_SERVER['HTTP_AUTHORIZATION'] ;
    $msgBuf = hash_hmac('sha256', $payload, base64_decode($teams_api_key), true);
    $msgHash = 'HMAC ' . base64_encode($msgBuf);

    // Get question from the request
    $text = json_decode($payload)->text;

    // If authenticated, run the AI Engine query
    if ($msgHash == $auth) {
        global $mwai_core;
        $query = new Meow_MWAI_QueryText();
        $query->setMaxTokens(1024);
        $query->setTemperature(0.8);
        $query->setMaxSentences(15);
        $query->setContext("Your context here");
        $query->setPrompt($text);
        $query->setEnv("chatbot");
        $query->setModel("gpt-3.5-turbo");
        $query->setMode("chat");
        $query->setService("openai");

        // Return query result
        echo '{"type":"message", "text": '. json_encode($mwai_core->ai->run($query)->result) .'}';
    }

    // If not authenticated, return error
    else {
        echo '{"type":"message", "text": "Error: Not authenticated"}';
    }
}

// If not POST request, return error
else {
    echo '{"type":"message", "text": "Error: POST request required"}';
}
