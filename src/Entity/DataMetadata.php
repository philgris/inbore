<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataMetadata
 *
 * @ORM\Table(name="data_metadata", indexes={@ORM\Index(name="fk_DATA_METADATA_id_mammifere", columns={"id_mammifere"}), @ORM\Index(name="fk_DATA_METADATA_code_type_analyse", columns={"code_type_analyse"}), @ORM\Index(name="fk_DATA_METADATA_code_responsable", columns={"code_responsable"})})
 * @ORM\Entity
 */
class DataMetadata
{
    /**
     * @var string
     *
     * @ORM\Column(name="idmetadata", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idmetadata;

    /**
     * @var string
     *
     * @ORM\Column(name="code_responsable", type="string", length=50, nullable=false)
     */
    private $codeResponsable;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code_projet", type="string", length=50, nullable=true)
     */
    private $codeProjet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code_protocole", type="string", length=50, nullable=true)
     */
    private $codeProtocole;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_envoi", type="integer", nullable=true)
     */
    private $idEnvoi;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_analyse", type="date", nullable=true)
     */
    private $dateAnalyse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fiche_protocole", type="string", length=50, nullable=true)
     */
    private $ficheProtocole;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_projet_inter", type="string", length=50, nullable=true)
     */
    private $nomProjetInter;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url_projet_inter", type="string", length=255, nullable=true)
     */
    private $urlProjetInter;

    /**
     * @var string|null
     *
     * @ORM\Column(name="stage_type", type="string", length=50, nullable=true)
     */
    private $stageType;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_debut", type="date", nullable=true)
     */
    private $dateDebut;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_fin", type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_operateur", type="string", length=255, nullable=true)
     */
    private $nomOperateur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="appareil", type="string", length=255, nullable=true)
     */
    private $appareil;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaires", type="text", length=65535, nullable=true)
     */
    private $commentaires;

    /**
     * @var bool
     *
     * @ORM\Column(name="valid", type="boolean", nullable=false)
     */
    private $valid = '0';

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

    /**
     * @var \MammifereIndividu
     *
     * @ORM\ManyToOne(targetEntity="MammifereIndividu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_mammifere", referencedColumnName="idmammifere")
     * })
     */
    private $idMammifere;

    /**
     * @var \TypeAnalyse
     *
     * @ORM\ManyToOne(targetEntity="TypeAnalyse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_type_analyse", referencedColumnName="idtype_analyse")
     * })
     */
    private $codeTypeAnalyse;


}
