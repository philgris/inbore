<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dissection
 *
 * @ORM\Table(name="dissection", uniqueConstraints={@ORM\UniqueConstraint(name="fk_DISSECTION_num_collec", columns={"num_collec"})}, indexes={@ORM\Index(name="fk_DISSECTION_conclusion_autopsie", columns={"conclusion_autopsie"}), @ORM\Index(name="fk_DISSECTION_MAMMIFERE_INDIVIDU1", columns={"id_mammifere"}), @ORM\Index(name="fk_DISSECTION_id_etat_dcc", columns={"id_etat_dcc"})})
 * @ORM\Entity
 */
class Dissection
{
    /**
     * @var int
     *
     * @ORM\Column(name="iddissection", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iddissection;

    /**
     * @var string|null
     *
     * @ORM\Column(name="num_collec", type="string", length=50, nullable=true)
     */
    private $numCollec;

    /**
     * @var bool
     *
     * @ORM\Column(name="prelevement_dispo", type="boolean", nullable=false)
     */
    private $prelevementDispo = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="autopsie_date", type="date", nullable=true)
     */
    private $autopsieDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="protocole", type="integer", nullable=true)
     */
    private $protocole;

    /**
     * @var string|null
     *
     * @ORM\Column(name="examinateur", type="string", length=255, nullable=true)
     */
    private $examinateur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lieu_examen", type="string", length=255, nullable=true)
     */
    private $lieuExamen;

    /**
     * @var bool
     *
     * @ORM\Column(name="rapport_autopsie_dispo", type="boolean", nullable=false)
     */
    private $rapportAutopsieDispo = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="rapport_autopsie", type="string", length=255, nullable=true)
     */
    private $rapportAutopsie;

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
     * @var \ConclusionAutopsie
     *
     * @ORM\ManyToOne(targetEntity="ConclusionAutopsie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="conclusion_autopsie", referencedColumnName="idconclusion_autopsie")
     * })
     */
    private $conclusionAutopsie;

    /**
     * @var \EtatDcc
     *
     * @ORM\ManyToOne(targetEntity="EtatDcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_etat_dcc", referencedColumnName="idetat_dcc")
     * })
     */
    private $idEtatDcc;

    /**
     * @var \MammifereIndividu
     *
     * @ORM\ManyToOne(targetEntity="MammifereIndividu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_mammifere", referencedColumnName="idmammifere")
     * })
     */
    private $idMammifere;


}
