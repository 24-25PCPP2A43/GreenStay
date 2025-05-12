<?php
// Fichier: Service/EmailService.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../vendor/autoload.php';

class EmailService {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);

        // Configuration Mailtrap avec vos nouveaux identifiants
        $this->mailer->isSMTP();
        $this->mailer->Host       = 'sandbox.smtp.mailtrap.io';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = '393bd0144b5494';
        $this->mailer->Password   = 'cc172fffc92c31';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = 2525;

        // Configuration de l'expéditeur
        $this->mailer->setFrom('reservations@wooxtravel.com', 'WoOx Travel');

        // Désactiver la vérification SSL pour le développement
        $this->mailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
    }

    /**
     * Envoie un email
     *
     * @param string $to Adresse email du destinataire
     * @param string $name Nom du destinataire
     * @param string $subject Sujet de l'email
     * @param string $body Corps du message (format texte)
     * @return bool Succès ou échec de l'envoi
     */
    public function send($to, $name, $subject, $body) {
        try {
            // Réinitialiser les destinataires (au cas où la méthode est appelée plusieurs fois)
            $this->mailer->clearAddresses();

            // Configuration du destinataire
            $this->mailer->addAddress($to, $name);

            // Configuration du contenu
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $this->getHtmlTemplate($name, $body);
            $this->mailer->AltBody = strip_tags($body);

            // Envoi de l'email
            $this->mailer->send();

            // Log de l'envoi réussi
            error_log("✅ Email envoyé avec succès à {$to} (via Mailtrap)");

            return true;
        } catch (Exception $e) {
            // Log de l'erreur
            error_log("❌ Erreur d'envoi d'email à {$to}: " . $this->mailer->ErrorInfo);

            return false;
        }
    }

    /**
     * Génère un template HTML pour l'email
     *
     * @param string $clientName Nom du client
     * @param string $messageContent Contenu du message
     * @return string Template HTML
     */
    private function getHtmlTemplate($clientName, $messageContent) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>WoOx Travel - Confirmation de Réservation</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                }
                .header {
                    background-color: #00c4cc;
                    color: white;
                    padding: 20px;
                    text-align: center;
                }
                .content {
                    padding: 20px;
                    background-color: #f9f9f9;
                }
                .footer {
                    background-color: #333;
                    color: white;
                    padding: 10px 20px;
                    text-align: center;
                    font-size: 12px;
                }
                .button {
                    display: inline-block;
                    background-color: #00c4cc;
                    color: white;
                    text-decoration: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    margin-top: 15px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>WoOx Travel</h1>
                <p>Confirmation de votre réservation</p>
            </div>
            <div class="content">
                <p>Bonjour ' . htmlspecialchars($clientName) . ',</p>
                <p>Merci d\'avoir réservé avec WoOx Travel !</p>
                ' . nl2br(htmlspecialchars($messageContent)) . '
                <p>Nous sommes impatients de vous accueillir.</p>
                <p>
                    <a href="https://wooxtravel.com/ma-reservation" class="button">Voir ma réservation</a>
                </p>
            </div>
            <div class="footer">
                <p>&copy; ' . date('Y') . ' WoOx Travel. Tous droits réservés.</p>
                <p>Si vous avez des questions, n\'hésitez pas à nous contacter.</p>
            </div>
        </body>
        </html>';
    }
}