<?php

namespace Smart\CoreBundle\Logger;

use Doctrine\ORM\EntityManagerInterface;
use Smart\CoreBundle\Entity\Log\HistorizableInterface;

/**
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class HistoryLogger
{
    public const EMAIL_SENT_CODE = 'email.sent';
    public const CREATED_CODE = 'crt';
    public const UPDATED_CODE = 'upd';
    public const ARCHIVED_CODE = 'arc';
    public const STRIPE_CODE = 'stripe';
    public const API_CODE = 'api';
    public const IMPORT_CODE = 'import';
    public const CRON_CODE = 'cron';

    public const CODE_PROPERTY = 'code'; // Code spécifique pour positionné l'icon et sa couleur
    public const DATE_PROPERTY = 'date'; // Date de la ligne
    public const CONTEXT_PROPERTY = 'ctxt'; // Context/Interface qui a déclencher l'ajout de la ligne d'historique
    public const ORIGIN_PROPERTY = 'orgn'; // Clé de trad de l'origine pour spécifier au sein du context quel action à déclencher l'ajout de la ligne
    public const USER_PROPERTY = 'user'; // __toString du user si défini qui a déclenché l'ajout de l'historique
    public const USER_PROFILE_PROPERTY = 'user_prf'; // Profile de l'utilisateur
    public const TITLE_PROPERTY = 'title'; // Ligne de titre de l'historique (utile dans le cas où il n'est pas déduit du code, ex: envoi des emails)
    public const RECIPIENT_PROPERTY = 'recipient'; // Email(s) destinataire pour les mails
    public const EMAIL_ID_PROPERTY = 'email_id'; // Uuid des emails envoyés avec API ou SMTP Custom pour les webhooks
    public const EMAIL_LAST_STATUS_PROPERTY = 'email_last_status'; // Dernier status d'email reçu pour l'email envoyé dans la ligne d'historique
    public const EMAIL_LAST_STATUS_AT_PROPERTY = 'email_last_status_at'; // Date du dernier status d'email
    public const EMAIL_STATUS_HISTORY_PROPERTY = 'email_status_history'; // Détail complet de l'historique des status de l'email envoyé
    public const COMMENT_PROPERTY = 'comment'; // Commentaire de l'historique (style discussion)
    public const DESCRIPTION_PROPERTY = 'desc'; // Description complémentaire
    public const DIFF_PROPERTY = 'diff'; // si code UPDATED_CODE alors diff contient le détail des modifs
    public const STATUS_PROPERTY = 'status'; // si UPDATED_CODE + entity implement HistorizableStatusInterface alors contient le changement des status
    public const STATUS_CODE_PROPERTY = 'status_code'; // Possible de définir un status code pour les appels API
    public const INTERNAL_PROPERTY = 'internal'; // Flag pour identifier les lignes internes
    public const SUCCESS_PROPERTY = 'success'; // Flag pour identifier les lignes d'update de status qui marque un succès
    public const CRON_ID_PROPERTY = 'cron_id'; // ID du monitoring cron lié à l'ajout de l'historique
    public const API_ID_PROPERTY = 'api_id'; // ID du monitoring ApiCall lié à l'ajout de l'historique

    protected EntityManagerInterface $entityManager;
    private ?string $context = null;
    private ?string $origin = null;
    private ?string $user = null;
    private ?string $userProfile = null;
    protected bool $flushLog = false;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $data Donnée de la ligne d'historique. Array (et non objet) pour être le plus performant possible dans HistoryDoctrineListener
     *  [
     *      'ctxt' => 'admin', // (admin, app, api, cron ...)
     *      'orgn' => 'h.crt_f',
     *      'user' => 'Mathieu Ducrot',
     *      'user_prf' => 'administrator',
     *      'title' => 'Demande de changement de mot de passe',
     *      'recipient' => 'test@email.fr',
     *      'email_id' => 'test@email.fr',
     *      'comment' => 'Commentaire optionnel visible qui peut être rendu avec de l'html',
     *      'diff' => [
     *          'firstName' => [ // champ cible de la modif
     *              'f' => 'Math', // from valeur avant
     *              't' => 'Mathieu', // to valeur après
     *          ]
     *      ],
     *      'status' => [ // si UPDATED_CODE + entity implement HistorizableStatusInterface + changement de status
     *          'f' => 'a_valider', // Code de l'enum avant le changement
     *          't' => 'termine', // Enum de l'enum après le changement
     *      ]
     *  ]
     */
    public function log(HistorizableInterface $entity, ?string $code = null, array $data = []): void
    {
        $history = array_merge([self::DATE_PROPERTY => time()], $data);
        if ($code !== null) {
            $history[self::CODE_PROPERTY] = $code;
        }
        if ($this->context !== null) {
            $history[self::CONTEXT_PROPERTY] = $this->context;
        }
        if ($this->origin !== null) {
            $history[self::ORIGIN_PROPERTY] = $this->origin;
        }
        if ($this->user !== null) {
            $history[self::USER_PROPERTY] = $this->user;
        }
        if ($this->userProfile !== null) {
            $history[self::USER_PROFILE_PROPERTY] = $this->userProfile;
        }

        $entity->addHistory($history);

        if ($this->flushLog) {
            $this->entityManager->flush();
        }
    }

    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    public function setOrigin(?string $origin): void
    {
        $this->origin = $origin;
    }

    public function setUser(?string $user): void
    {
        $this->user = $user;
    }

    public function setUserProfile(?string $userProfile): void
    {
        $this->userProfile = $userProfile;
    }

    public function setFlushLog(bool $flushLog): void
    {
        $this->flushLog = $flushLog;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
