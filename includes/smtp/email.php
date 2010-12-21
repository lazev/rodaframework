<?
require_once('class.phpmailer.php');

$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';
$mail->SetLanguage('br', ''); //língua a ser utilizadda
$mail->SMTP_PORT = '587'; //porta de smt a ser utilizada. Neste caso, a 587 que o GMail utiliza
$mail->SMTPSecure = 'tls'; //tipo de comunicação a ser utilizada, no caso, a TLS do GMail
$mail->IsSMTP(); //email para utilizar protocolo SMTP
$mail->Host = 'smtp.gmail.com'; //endereço do servidor smtp do GMail
$mail->SMTPAuth = true; //autenticação SMTP, no caso do GMail, é necessário
$mail->Username = 'nao_responder@zipline.com.br'; //usuário SMTP do GMail
$mail->Password = '8687B6'; //senha do usuário SMTP do GMail
$mail->WordWrap = 75; // quebra linha sempre que uma linha atingir os caracteres (inicial: 50)

$mail->IsHTML($html);
$mail->From = $demail;
$mail->FromName = $denome;
$mail->AddAddress($para);
$mail->AddReplyTo($demail, $de);
$mail->Subject = $assunto;
$mail->Body = $mensagem;

/*
Opcionais:
$mail->AddAttachment('/var/tmp/file.tar.gz');         // adc arquivo anexo.
$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // adc outro arquivo anexo com nome (opcional).
$mail->AltBody = 'Este é o corpo da mensagem para usuários que possuem a opção de ver o html do email desativada em seu cliente de email';
*/


if(!$mail->Send()) $resposta = false; //'Erro no envio: '. $mail->ErrorInfo;
else $resposta = true; //'Mensagem enviada';
?>
