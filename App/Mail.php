<?php

namespace App;

header('Content-type: text/html; charset=utf-8');
//use Mailgun\Mailgun;
//use App\Config;

use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\SMTP;
//use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

/**
 * Mail
 */
class Mail
{
    /**
     * Send a message
     * 
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     * 
     * @return mixed
     */
    /*
    public static function send($to, $subject, $text, $html)
    {
        // First, instantiate the SDK with your API credentials
        $mg = Mailgun::create(Config::MAILGUN_API_KEY); // For EU servers //, 'https://api.eu.mailgun.net'
        $domain = Config::MAILGUN_DOMAIN;
        // Now, compose and send your message.
        // $mg->messages()->send($domain, $params);
        $mg->messages()->send($domain, [
        'from'    => 'bob@example.com',
        'to'      => $to,
        'subject' => $subject,
        'text'    => $text,
        'html'    => $html
        ]);

    }
    */
    public static function send($to, $subject, $text, $html) 
    {
        //Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'host';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Username = 'username'; 
        $mail->Password = 'password'; 
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('your-email@gmail.com', 'Name');
        $mail->addAddress($to, '');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body =  $html; 
        $mail->AltBody = $text;
        $mail->send();
    }
}