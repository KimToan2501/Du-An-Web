<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail
{
  private $mailer;

  public function __construct()
  {
    $this->mailer = new PHPMailer(true);
    $this->configure();
  }

  private function configure()
  {
    try {
      // Server settings
      $this->mailer->isSMTP();
      $this->mailer->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
      $this->mailer->SMTPAuth   = true;
      $this->mailer->Username   = $_ENV['MAIL_USERNAME'] ?? '';
      $this->mailer->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
      $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $this->mailer->Port       = $_ENV['MAIL_PORT'] ?? 587;
      $this->mailer->CharSet    = 'UTF-8';

      // Default sender
      $this->mailer->setFrom(
        $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@pawspa.com',
        $_ENV['MAIL_FROM_NAME'] ?? 'Pawspa'
      );
    } catch (Exception $e) {
      error_log('Mail configuration error: ' . $e->getMessage());
    }
  }

  public function send($to, $subject, $body, $altBody = '')
  {
    try {
      // Recipients
      $this->mailer->addAddress($to);

      // Content
      $this->mailer->isHTML(true);
      $this->mailer->Subject = $subject;
      $this->mailer->Body    = $body;

      if ($altBody) {
        $this->mailer->AltBody = $altBody;
      }

      $result = $this->mailer->send();

      // Clear recipients for next email
      $this->mailer->clearAddresses();

      return $result;
    } catch (Exception $e) {
      error_log('Mail send error: ' . $e->getMessage());
      return false;
    }
  }

  public function sendMultiple($recipients, $subject, $body, $altBody = '')
  {
    $results = [];

    foreach ($recipients as $email) {
      $results[$email] = $this->send($email, $subject, $body, $altBody);
    }

    return $results;
  }

  public function addAttachment($path, $name = '')
  {
    try {
      $this->mailer->addAttachment($path, $name);
      return true;
    } catch (Exception $e) {
      error_log('Mail attachment error: ' . $e->getMessage());
      return false;
    }
  }

  public function setReplyTo($email, $name = '')
  {
    try {
      $this->mailer->addReplyTo($email, $name);
      return true;
    } catch (Exception $e) {
      error_log('Mail reply-to error: ' . $e->getMessage());
      return false;
    }
  }

  public function addCC($email, $name = '')
  {
    try {
      $this->mailer->addCC($email, $name);
      return true;
    } catch (Exception $e) {
      error_log('Mail CC error: ' . $e->getMessage());
      return false;
    }
  }

  public function addBCC($email, $name = '')
  {
    try {
      $this->mailer->addBCC($email, $name);
      return true;
    } catch (Exception $e) {
      error_log('Mail BCC error: ' . $e->getMessage());
      return false;
    }
  }
}
