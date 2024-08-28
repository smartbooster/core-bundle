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
    private ?string $title = null;
    private ?string $comment = null;
    private ?string $description = null;
    private ?bool $internal = null;
    private ?bool $success = null;
    private ?int $cronId = null;
    private ?int $apiId = null;
    protected bool $flushLog = false;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * MDT if you need to flush the log, be sure to call it before calling the log method
     */
    public function setFlushLog(bool $flushLog): static
    {
        $this->flushLog = $flushLog;

        return $this;
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
        foreach (
            [
                self::CODE_PROPERTY => $code,
                // MDT check if possible refacto based on dynamic constante/properties
                self::CONTEXT_PROPERTY => $this->context,
                self::ORIGIN_PROPERTY => $this->origin,
                self::USER_PROPERTY => $this->user,
                self::USER_PROFILE_PROPERTY => $this->userProfile,
                self::TITLE_PROPERTY => $this->title,
                self::COMMENT_PROPERTY => $this->comment,
                self::DESCRIPTION_PROPERTY => $this->description,
                self::INTERNAL_PROPERTY => $this->internal,
                self::SUCCESS_PROPERTY => $this->success,
                self::CRON_ID_PROPERTY => $this->cronId,
                self::API_ID_PROPERTY => $this->apiId,
            ] as $key => $value
        ) {
            if ($value !== null) {
                $history[$key] = $value;
            }
        }

        // If the history log is an update but without data we don't log it to the database.
        if (
            $code === self::UPDATED_CODE
            && !isset($history[self::DIFF_PROPERTY])
            && $this->title === null
            && $this->comment === null
            && $this->description === null
        ) {
            return;
        }
        $entity->addHistory($history);

        if ($this->flushLog) {
            $this->entityManager->flush();
        }
    }

    public function setContext(string $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function setOrigin(?string $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    public function setUser(?string $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function setUserProfile(?string $userProfile): static
    {
        $this->userProfile = $userProfile;

        return $this;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function setInternal(?bool $internal): static
    {
        $this->internal = $internal;

        return $this;
    }

    public function setSuccess(?bool $success): static
    {
        $this->success = $success;

        return $this;
    }

    public function setCronId(?int $cronId): static
    {
        $this->cronId = $cronId;

        return $this;
    }

    public function setApiId(?int $apiId): static
    {
        $this->apiId = $apiId;

        return $this;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
