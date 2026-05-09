<?php

class ReclamationNotifier
{
    public static function sendResponseNotification(
        string $email,
        string $objet,
        string $reponse
    ): void {

        $subject = "Réponse à votre réclamation";

        $message = "
        Bonjour,

        Une réponse a été ajoutée à votre réclamation :

        Objet : $objet

        Réponse :
        $reponse

        Merci.
        ";

        @mail($email, $subject, $message);
    }
}