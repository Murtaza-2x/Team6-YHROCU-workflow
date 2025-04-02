<?php
/*
-------------------------------------------------------------
Function: inc_email.php
Description:
- Sends an email notification to the provided email address.
- Can be used for both task creation and task updates.
- Uses PHPMailer's SMTP configuration for Mailtrap.
-------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendTaskEmail($toEmail, $subject, $messageBody) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io'; // Use SMTP server
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '15ae02232bf29d'; // Mailtrap SMTP username
        $mail->Password = '68f352cc509c23'; // Mailtrap SMTP password

        // Email details
        $mail->setFrom('yhrocunotifications@gmail.com', 'Task Notification');
        $mail->addAddress($toEmail);

        // Email body
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $messageBody;

        // Send email
        $mail->send();
        echo "<p class='SUCCESS-MESSAGE'>Message has been sent successfully.</p>";
    } catch (Exception $e) {
        // Log any errors
        error_log("Mailtrap email failed: {$mail->ErrorInfo}");
    }
}

?>
