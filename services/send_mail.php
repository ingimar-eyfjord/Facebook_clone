<?php

$whereSuccess = 'profile';
$where = 'signup';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'mail/src/Exception.php';
require 'mail/src/PHPMailer.php';
require 'mail/src/SMTP.php';
$mail = new PHPMailer(true);
try {
                //Server settings
                $mail->SMTPDebug = 0;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'ingimar.web.dev@gmail.com';               //SMTP username
                $mail->Password   = "zUKdCdpnU5L2GbTz";                              //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                //Recipients
                $mail->setFrom('ingimar.web.dev@gmail.com', 'Ingimar Smarason');
                $mail->addAddress($email, $username);     //Add a recipient
                $mail->addReplyTo('ingimar.web.dev@gmail.com', 'Information');
                // $mail->addCC('');
                // $mail->addBCC('');
                //Attachments
                // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body    =  html_entity_decode($send_email_body);
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                $mail->send();
            } catch (Exception $e) {
                http_response_code(500);
                echo "Message could not be sent.";
                exit();
            }