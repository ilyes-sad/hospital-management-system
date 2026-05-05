<?php

/**
 * Notification patient après réponse finale (Acceptée / Refusée).
 */
class ReclamationNotifier
{
    public static function notifyPatientFinalResponse(
        string $emailPatient,
        string $nomPatient,
        int    $idReclamation,
        string $statutLabel,
        string $messageReponse,
        string $dateReponse,
        string $nomHopital,
        string $nomService
    ): bool {
        $emailPatient = trim($emailPatient);
        if ($emailPatient === '' || !filter_var($emailPatient, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $from     = getenv('MAIL_FROM')      ?: 'noreply@localhost';
        $fromName = getenv('MAIL_FROM_NAME') ?: 'Plateforme réclamations';

        $subject = 'Réponse à votre réclamation';
        $lines   = [
            'Bonjour' . ($nomPatient !== '' ? ' ' . $nomPatient : '') . ',',
            '',
            'Vous avez reçu une réponse concernant votre réclamation n°' . $idReclamation . '.',
            '',
            'Statut : ' . $statutLabel,
            'Hôpital / établissement : ' . ($nomHopital !== '' ? $nomHopital : '—'),
            'Service : ' . ($nomService !== '' ? $nomService : '—'),
            'Date de la réponse : ' . $dateReponse,
            '',
            'Message :',
            $messageReponse,
            '',
            'Cordialement,',
            $fromName,
        ];
        $body = implode("\r\n", $lines);

        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . self::encodeHeaderName($fromName) . ' <' . $from . '>',
            'Reply-To: ' . $from,
            'X-Mailer: PHP/' . PHP_VERSION,
        ]);

        return @mail($emailPatient, self::encodeSubject($subject), $body, $headers);
    }

    private static function encodeSubject(string $subject): string
    {
        return '=?UTF-8?B?' . base64_encode($subject) . '?=';
    }

    private static function encodeHeaderName(string $name): string
    {
        return '=?UTF-8?B?' . base64_encode($name) . '?=';
    }
}
