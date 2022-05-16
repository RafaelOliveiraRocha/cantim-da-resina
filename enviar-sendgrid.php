<?php

// Configure no ambiente do processo PHP; arquivos .env não são carregados aqui.
$api_key = trim((string) getenv('SENDGRID_API_KEY'));
$email_site = trim((string) getenv('SENDGRID_FROM_EMAIL'));
$nome_site = "Cantim da Resina";

function responder_formulario($status, $mensagem) {
    http_response_code($status);
    header('Content-Type: text/html; charset=UTF-8');
    $id = $status === 200 ? 'form-send' : 'form-erro';
    echo '<div class="form-content" id="' . $id . '"><p>' . $mensagem . '</p></div>';
}

if ($api_key === '' || $email_site === '') {
    responder_formulario(503, 'Envio indisponível: configure SENDGRID_API_KEY e SENDGRID_FROM_EMAIL no ambiente do servidor.');
    exit;
}

try {
    require("./sendgrid-php/sendgrid-php.php");

    $email_user = $_POST["email"];
    $nome_user = $_POST["nome"];

    $body_content = "";
    foreach( $_POST as $field => $value) {
        if( $field !== "leaveblank" && $field !== "dontchange" && $field !== "enviar") {
            $sanitize_value = filter_var($value, FILTER_SANITIZE_STRING);
            $body_content .= "$field: $value \n";
        }
    }

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom($email_site, $nome_site);
    $email->addTo($email_site, $nome_site);
    $email->setReplyTo($email_user, $nome_user);
    $email->setSubject("Formulário Cantim da Resina");
    $email->addContent("text/plain", $body_content);

    $sendgrid = new \SendGrid($api_key);
    $response = $sendgrid->send($email);
    $status = $response->statusCode();
    if ($status >= 200 && $status < 300) {
        responder_formulario(200, 'Formulário enviado.');
    } else {
        responder_formulario(503, 'Não foi possível enviar o formulário. Tente novamente mais tarde.');
    }
} catch (\Throwable $e) {
    responder_formulario(503, 'Não foi possível enviar o formulário. Tente novamente mais tarde.');
}
