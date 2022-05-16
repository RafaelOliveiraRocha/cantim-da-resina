<?php

require("./sendgrid-php/sendgrid-php.php");

$email_site = "contato@cantimdaresina.com";
$nome_site = "Cantim da Resina";

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

$sendgrid = new \SendGrid("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");
try {
    $response = $sendgrid->send($email);
    print $response->statusCode() . "\n";
    print_r($response->headers());
    print $response->body() . "\n";
} catch (Exception $e) {
    echo "Caught exception: ". $e->getMessage() ."\n";
}