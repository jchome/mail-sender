<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

// The details of your SMTP service, e.g. Gmail.
define('CONTACTFORM_SMTP_HOSTNAME', 'smtp.host.com');
define('CONTACTFORM_SMTP_USERNAME', 'username');
define('CONTACTFORM_SMTP_PASSWORD', 'password');

// Which SMTP port and encryption type to use. The default is probably fine for most use cases.
define('CONTACTFORM_SMTP_PORT', 587);
define('CONTACTFORM_SMTP_ENCRYPTION', 'tls');

// Character encoding settings. The default is probably fine for most use cases.
define('CONTACTFORM_MAIL_CHARSET', 'utf-8'); // Can be: us-ascii, iso-8859-1, utf-8. Default: iso-8859-1.
define('CONTACTFORM_MAIL_ENCODING', '8bit'); // Can be: 7bit, 8bit, base64, binary, quoted-printable. Default: 8bit.


$json = file_get_contents('php://input');
$data = json_decode($json);

$checkArray = explode("-", base64_decode($data->key));

$timeRequest = intval($checkArray[count($checkArray)-2]);
$ipAddressRequest = $checkArray[count($checkArray)-1];

if( time() - $timeRequest > 10){
    print("Time over.");
    exit;
}
if( $_SERVER['REMOTE_ADDR'] != $ipAddressRequest){
    print("Not same IP.");
    exit;
}

$name = $data->name;
$email = $data->email;
$comment = $data->request;
/*
// In-host email sending
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
$from = "username@host.com";
$to = $data->email;
$subject = "Subject";
$message = "This is the plain-text message";
$headers = "From :" . $from;
$resultIsOk = mail($to, $subject, $message, $headers);

if( $resultIsOk){
    print("OK");
}else{
    print("Error");
}*/

$mail = new PHPMailer(true);
try {
    // Server settings
    //$mail->setLanguage(CONTACTFORM_LANGUAGE);
    //$mail->SMTPDebug = CONTACTFORM_PHPMAILER_DEBUG_LEVEL;
    $mail->isSMTP();
    $mail->Host = CONTACTFORM_SMTP_HOSTNAME;
    $mail->SMTPAuth = true;
    $mail->Username = CONTACTFORM_SMTP_USERNAME;
    $mail->Password = CONTACTFORM_SMTP_PASSWORD;
    $mail->SMTPSecure = CONTACTFORM_SMTP_ENCRYPTION;
    $mail->Port = CONTACTFORM_SMTP_PORT;
    $mail->CharSet = CONTACTFORM_MAIL_CHARSET;
    $mail->Encoding = CONTACTFORM_MAIL_ENCODING;

    // Recipients
    $mail->setFrom("username@host.com", "User Name");
    $mail->addAddress($data->email);
    //$mail->addReplyTo($_POST['email'], $_POST['name']);

    // Content
    $mail->isHTML(true);
    $mail->Subject = iconv("UTF-8", "ISO-8859-1//TRANSLIT", "Subject");
    $mail->Body    = <<<EOT
<html>
<body>
Hi there, <br>
<p>This is the HTML message. 
</p>
<p>
Sincerly
</p>
</body>
</html>
EOT;

    $resultIsOk = $mail->send();
    if( !$resultIsOk){
        print($mail->ErrorInfo);
        return;
    }

    // Second mail
    $mail->clearAddresses();

    $mail->setFrom("service-no-reply@host.com", "No-reply of Service");
    $mail->addAddress("myself@host.com");
    $mail->Subject = "New message";

    $mail->Body = <<<EOT
    <html>
    <body>
    <p>a New contact form was filled :</p>
    <ul>
        <li>Name : {$data->name}</li>
        <li>Email : {$data->email}</li>
        <li>comment : <pre>{$data->request}</pre></li>
    </ul>
    <p>
    </p>
    </body>
    </html>
    EOT;

    $resultIsOk = $mail->send();
    if($resultIsOk){
        print("OK");
    }else{
        print($mail->ErrorInfo);
    }
    
} catch (Exception $e) {
    print($mail->ErrorInfo);
}

?>