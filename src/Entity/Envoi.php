<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Envoi
 *
 * @ORM\Table(name="envoi")
 * @ORM\Entity
 */
class Envoi
{
    /**
     * @var int
     *
     * @ORM\Column(name="idenvoi", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idenvoi;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_envoi", type="date", nullable=true)
     */
    private $dateEnvoi;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lieu_envoi", type="string", length=255, nullable=true)
     */
    private $lieuEnvoi;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_demande", type="date", nullable=true)
     */
    private $dateDemande;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_retour", type="date", nullable=true)
     */
    private $dateRetour;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_demandeur", type="string", length=255, nullable=true)
     */
    private $nomDemandeur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="institut_demandeur", type="string", length=255, nullable=true)
     */
    private $institutDemandeur;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_accord_cs", type="date", nullable=true)
     */
    private $dateAccordCs;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_mise_a_dispo", type="date", nullable=true)
     */
    private $dateMiseADispo;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_restitution", type="date", nullable=true)
     */
    private $dateRestitution;

    /**
     * @var string|null
     *
     * @ORM\Column(name="budget_previsionnel", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $budgetPrevisionnel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="budget_final", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $budgetFinal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaires", type="text", length=65535, nullable=true)
     */
    private $commentaires;

    /**
     * @var string|null
     *
     * @ORM\Column(name="No_Facture", type="string", length=50, nullable=true)
     */
    private $noFacture;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_of_creation", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateOfCreation = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_of_update", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateOfUpdate = 'CURRENT_TIMESTAMP';

    /**
     * @var int|null
     *
     * @ORM\Column(name="creation_user_name", type="bigint", nullable=true, options={"default"="1"})
     */
    private $creationUserName = '1';

    /**
     * @var int|null
     *
     * @ORM\Column(name="update_user_name", type="bigint", nullable=true, options={"default"="1"})
     */
    private $updateUserName = '1';


}
