<?php
  
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email_envio = trim((string) getenv('SMTP_FROM_EMAIL')); // Remetente e destinatário
$email_pass = trim((string) getenv('SMTP_PASSWORD'));
$host_smtp = trim((string) getenv('SMTP_HOST'));

if ($email_envio === '' || $email_pass === '' || $host_smtp === '') {
    http_response_code(503);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<div class="form-content" id="form-erro"><p>Envio indisponível: configure SMTP_FROM_EMAIL, SMTP_PASSWORD e SMTP_HOST no ambiente do servidor.</p></div>';
    exit;
}

require "./PHPMailer/src/Exception.php";
require "./PHPMailer/src/PHPMailer.php";
require "./PHPMailer/src/SMTP.php";
  
// Mudar Aqui o e-mail

$site_name = "Cantim da Resina"; // Nome do Site
$site_url = "https://example.invalid"; // Placeholder; campo não usado no envio

$host_port = "587"; // Porta do Host, geralmente 465 ou 587


// Não mudar abaixo:
$email = $_POST["email"];
$nome = $_POST["nome"];

// Loop por cada field do formulário
$body_content = "";
foreach( $_POST as $field => $value) {
  if( $field !== "leaveblank" && $field !== "dontchange" && $field !== "enviar") {
    $sanitize_value = filter_var($value, FILTER_SANITIZE_STRING);
    $body_content .= "$field: $value \n";
  }
}

// Verifica se não é bot
$notbot = ($_POST["leaveblank"] === "") || ($_POST["dontchange"] === "http://");

if ($notbot) {

// Inicia o objeto PHPMailer
try {
  $mail = new PHPMailer(true);
  $mail->CharSet = "UTF-8";
  
  //$mail->SMTPDebug = 3; // Tire do comentário para debugar
  $mail->isSMTP();
  $mail->Host = $host_smtp;
  $mail->SMTPAuth = true;
  $mail->Username = $email_envio;
  $mail->Password = $email_pass;
  $mail->Port = $host_port; 
  $mail->SMTPSecure = "tsl"; //Se não tiver SSL use assim, com SSL coloque no SMTPSecure
  
  $mail->setFrom($email_envio, "Formulário - ". $nome);
  $mail->addAddress($email_envio, $site_name);
  $mail->addReplyTo($email, $nome);
  
  $mail->WordWrap = 70;
  $mail->Subject = "Formulário - " . $site_name . " - " . $nome;
  $mail->Body = $body_content;
  
  $mail->send();
?>

  <html>
    <head>
      <title>Formulário enviado</title>
      <meta http-equiv="refresh" content="10;URL="./"">
    </head>
    <body>
      <!-- Mensagem de sucesso -->
      <div class="form-content" id="form-send">
        <h2>Formulário enviado!</h2>
        <p>Em breve eu entro em contato com você.</p>
      </div>
    </body>
  </html>

<?php } catch (\Throwable $e) {
  http_response_code(500);
?>

  <html>
    <head>
      <title>Erro no envio</title>
      <meta http-equiv="refresh" content="10;URL="./"">
    </head>
    <body>
      <!-- Mensagem de erro -->
      <div class="form-content" id="form-erro">
        <h2>Um erro ocorreu!</h2>
        <p>Não foi possível enviar o formulário. Tente novamente mais tarde.</p>
      </div>
    </body>
  </html>

<?php
  }}
?>