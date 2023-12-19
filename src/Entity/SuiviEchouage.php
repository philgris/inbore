<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SuiviEchouage
 *
 * @ORM\Table(name="suivi_echouage", indexes={@ORM\Index(name="fk_SUIVI_ECHOUAGE_EVT_ECHOUAGE1", columns={"id_echouage"}), @ORM\Index(name="fk_SUIVI_ECHOUAGE_id_ncc", columns={"id_ncc"}), @ORM\Index(name="fk_SUIVI_ECHOUAGE_id_gie", columns={"id_gie"}), @ORM\Index(name="fk_SUIVI_ECHOUAGE_id_echouage", columns={"id_echouage", "code_capt"}), @ORM\Index(name="fk_SUIVI_ECHOUAGE_CODE_EXAMEN1", columns={"code_exam"}), @ORM\Index(name="fk_SUIVI_ECHOUAGE_id_etat_appel", columns={"id_etat_appel"}), @ORM\Index(name="fk_SUIVI_ECHOUAGE_id_echouage_type", columns={"id_echouage_type"}), @ORM\Index(name="fk_SUIVI_ECHOUAGE_code_capt", columns={"code_capt"}), @ORM\Index(name="fk_SUIVI_ECHOUAGE_id_etat_echouage", columns={"id_etat_echouage"}), @ORM\Index(name="fk_SUIVI_ECHOUAGE_id_mammifere", columns={"id_mammifere"})})
 * @ORM\Entity
 */
class SuiviEchouage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_gie", type="integer", nullable=true)
     */
    private $idGie;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_echou", type="date", nullable=true)
     */
    private $dateEchou;

    /**
     * @var string|null
     *
     * @ORM\Column(name="info_etat", type="string", length=50, nullable=true)
     */
    private $infoEtat;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="transit_date_entree", type="date", nullable=true)
     */
    private $transitDateEntree;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="transit_date_sortie", type="date", nullable=true)
     */
    private $transitDateSortie;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transit_centre", type="string", length=255, nullable=true)
     */
    private $transitCentre;

    /**
     * @var float|null
     *
     * @ORM\Column(name="transit_poids", type="float", precision=10, scale=0, nullable=true)
     */
    private $transitPoids;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="renflouage_date", type="date", nullable=true)
     */
    private $renflouageDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="renflouage_intervenant", type="string", length=255, nullable=true)
     */
    private $renflouageIntervenant;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="soin_date_entree", type="date", nullable=true)
     */
    private $soinDateEntree;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="soin_date_sortie", type="date", nullable=true)
     */
    private $soinDateSortie;

    /**
     * @var string|null
     *
     * @ORM\Column(name="soin_centre", type="string", length=255, nullable=true)
     */
    private $soinCentre;

    /**
     * @var float|null
     *
     * @ORM\Column(name="soin_sortie_poids", type="float", precision=10, scale=0, nullable=true)
     */
    private $soinSortiePoids;

    /**
     * @var string|null
     *
     * @ORM\Column(name="soin_lieu_relache", type="string", length=255, nullable=true)
     */
    private $soinLieuRelache;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="euthanasie_date", type="date", nullable=true)
     */
    private $euthanasieDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="euthanasie_veterinaire", type="string", length=255, nullable=true)
     */
    private $euthanasieVeterinaire;

    /**
     * @var string|null
     *
     * @ORM\Column(name="info_photo", type="text", length=65535, nullable=true)
     */
    private $infoPhoto;

    /**
     * @var bool
     *
     * @ORM\Column(name="examen_externe", type="boolean", nullable=false)
     */
    private $examenExterne = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="examen_interne", type="boolean", nullable=false)
     */
    private $examenInterne = '0';

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
     * @var \EchouageType
     *
     * @ORM\ManyToOne(targetEntity="EchouageType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_echouage_type", referencedColumnName="idechouage_type")
     * })
     */
    private $idEchouageType;

    /**
     * @var \EvtEchouage
     *
     * @ORM\ManyToOne(targetEntity="EvtEchouage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_echouage", referencedColumnName="idechouage")
     * })
     */
    private $idEchouage;

    /**
     * @var \CodeCapture
     *
     * @ORM\ManyToOne(targetEntity="CodeCapture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_capt", referencedColumnName="idcode_capture")
     * })
     */
    private $codeCapt;

    /**
     * @var \EtatDcc
     *
     * @ORM\ManyToOne(targetEntity="EtatDcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_etat_echouage", referencedColumnName="idetat_dcc")
     * })
     */
    private $idEtatEchouage;

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
     * @var \CodeExamen
     *
     * @ORM\ManyToOne(targetEntity="CodeExamen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_exam", referencedColumnName="idcode_examen")
     * })
     */
    private $codeExam;

    /**
     * @var \EtatDcc
     *
     * @ORM\ManyToOne(targetEntity="EtatDcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_etat_appel", referencedColumnName="idetat_dcc")
     * })
     */
    private $idEtatAppel;

    /**
     * @var \Ncc
     *
     * @ORM\ManyToOne(targetEntity="Ncc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_ncc", referencedColumnName="idncc")
     * })
     */
    private $idNcc;


}
