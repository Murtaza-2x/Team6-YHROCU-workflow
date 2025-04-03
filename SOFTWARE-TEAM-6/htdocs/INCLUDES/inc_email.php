<?php 
// inc_email.php will send emails

require __DIR__.'/../../libs/PHPMailer/src/PHPMailer.php';
require __DIR__.'/../../libs/PHPMailer/src/Exception.php';
require __DIR__.'/../../libs/PHPMailer/src/SMTPLphp';
require __DIR__.'/../../libs/PHPMailer/src/OAuth.php';
require __DIR__.'/../../libs/google-api-php-client

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// send email notifs
// @param string $to is asignee's email
// @param string $subject is the email subject
// @param string $emailMsg is the body text of the email
 function sendEmail($to, $subject, $emailMsg) {
    $mail = new PHPMailer(true);

    try {
        // Gmail OAuth credential
        $email = 'yhrocunotifications@gmail.com'
    }
 }