<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MammifereIndividu
 *
 * @ORM\Table(name="mammifere_individu", indexes={@ORM\Index(name="fk_MAMMIFERE_INDIVIDU_esp_id", columns={"esp_id"}), @ORM\Index(name="fk_MAMMIFERE_INDIVIDU_code_sexe", columns={"code_sexe"}), @ORM\Index(name="fk_MAMMIFERE_INDIVIDU_id_foetus", columns={"id_fiche_mere"}), @ORM\Index(name="fk_MAMMIFERE_INDIVIDU_id_fiche_mere", columns={"id_fiche_mere"})})
 * @ORM\Entity
 */
class MammifereIndividu
{
    /**
     * @var int
     *
     * @ORM\Column(name="idmammifere", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idmammifere;

    /**
     * @var string|null
     *
     * @ORM\Column(name="conf_esp", type="string", length=0, nullable=true)
     */
    private $confEsp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_mort", type="date", nullable=true)
     */
    private $dateMort;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bague_type", type="string", length=50, nullable=true)
     */
    private $bagueType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bague_num", type="string", length=50, nullable=true)
     */
    private $bagueNum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bague_coul", type="string", length=50, nullable=true)
     */
    private $bagueCoul;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bague_autres", type="string", length=255, nullable=true)
     */
    private $bagueAutres;

    /**
     * @var string|null
     *
     * @ORM\Column(name="balise_num", type="string", length=50, nullable=true)
     */
    private $baliseNum;

    /**
     * @var float|null
     *
     * @ORM\Column(name="poids_kg", type="float", precision=10, scale=0, nullable=true)
     */
    private $poidsKg;

    /**
     * @var float|null
     *
     * @ORM\Column(name="long_cm", type="float", precision=10, scale=0, nullable=true)
     */
    private $longCm;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prec_long", type="string", length=0, nullable=true)
     */
    private $precLong;

    /**
     * @var int|null
     *
     * @ORM\Column(name="circonf_cm", type="integer", nullable=true)
     */
    private $circonfCm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l2_cm", type="integer", nullable=true)
     */
    private $l2Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l3_cm", type="integer", nullable=true)
     */
    private $l3Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l4_cm", type="integer", nullable=true)
     */
    private $l4Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l5_cm", type="integer", nullable=true)
     */
    private $l5Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l6_cm", type="integer", nullable=true)
     */
    private $l6Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l7_cm", type="integer", nullable=true)
     */
    private $l7Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l8_cm", type="integer", nullable=true)
     */
    private $l8Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l9_cm", type="integer", nullable=true)
     */
    private $l9Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l10_cm", type="integer", nullable=true)
     */
    private $l10Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="l12_cm", type="integer", nullable=true)
     */
    private $l12Cm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ep13_mm", type="integer", nullable=true)
     */
    private $ep13Mm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ep14_mm", type="integer", nullable=true)
     */
    private $ep14Mm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ep15_mm", type="integer", nullable=true)
     */
    private $ep15Mm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inf_dpc", type="integer", nullable=true)
     */
    private $infDpc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inf_gpc", type="integer", nullable=true)
     */
    private $infGpc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sup_dpc", type="integer", nullable=true)
     */
    private $supDpc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sup_gpc", type="integer", nullable=true)
     */
    private $supGpc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inf_dc", type="integer", nullable=true)
     */
    private $infDc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inf_gc", type="integer", nullable=true)
     */
    private $infGc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sup_dc", type="integer", nullable=true)
     */
    private $supDc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sup_gc", type="integer", nullable=true)
     */
    private $supGc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inf_di", type="integer", nullable=true)
     */
    private $infDi;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inf_gi", type="integer", nullable=true)
     */
    private $infGi;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sup_di", type="integer", nullable=true)
     */
    private $supDi;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sup_gi", type="integer", nullable=true)
     */
    private $supGi;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inf_d", type="integer", nullable=true)
     */
    private $infD;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inf_g", type="integer", nullable=true)
     */
    private $infG;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sup_d", type="integer", nullable=true)
     */
    private $supD;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sup_g", type="integer", nullable=true)
     */
    private $supG;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaires", type="text", length=65535, nullable=true)
     */
    private $commentaires;

    /**
     * @var string|null
     *
     * @ORM\Column(name="condition_physique", type="string", length=0, nullable=true)
     */
    private $conditionPhysique;

    /**
     * @var bool
     *
     * @ORM\Column(name="abrasion", type="boolean", nullable=false)
     */
    private $abrasion = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="hematome", type="boolean", nullable=false)
     */
    private $hematome = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="coupe_nette", type="boolean", nullable=false)
     */
    private $coupeNette = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="perforation", type="boolean", nullable=false)
     */
    private $perforation = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="morsure", type="boolean", nullable=false)
     */
    private $morsure = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="malformation", type="boolean", nullable=false)
     */
    private $malformation = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="grosseur_tumeur", type="boolean", nullable=false)
     */
    private $grosseurTumeur = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="necrose_pathologique", type="boolean", nullable=false)
     */
    private $necrosePathologique = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="fracture", type="boolean", nullable=false)
     */
    private $fracture = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="corps_etranger", type="boolean", nullable=false)
     */
    private $corpsEtranger = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="parasite_externe", type="boolean", nullable=false)
     */
    private $parasiteExterne = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="avis_cause_mortalite", type="string", length=0, nullable=true)
     */
    private $avisCauseMortalite;

    /**
     * @var bool
     *
     * @ORM\Column(name="capture", type="boolean", nullable=false)
     */
    private $capture = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="collision", type="boolean", nullable=false)
     */
    private $collision = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="pathologie", type="boolean", nullable=false)
     */
    private $pathologie = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="predation_competition", type="boolean", nullable=false)
     */
    private $predationCompetition = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="indetermine", type="boolean", nullable=false)
     */
    private $indetermine = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="autre", type="boolean", nullable=false)
     */
    private $autre = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="aucune_lesion", type="integer", nullable=false)
     */
    private $aucuneLesion = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="lesion_indeterminee", type="integer", nullable=false)
     */
    private $lesionIndeterminee = '0';

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
     *   @ORM\JoinColumn(name="id_fiche_mere", referencedColumnName="idmammifere")
     * })
     */
    private $idFicheMere;

    /**
     * @var \CodeSexe
     *
     * @ORM\ManyToOne(targetEntity="CodeSexe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_sexe", referencedColumnName="idcode_sexe")
     * })
     */
    private $codeSexe;

    /**
     * @var \Taxon
     *
     * @ORM\ManyToOne(targetEntity="Taxon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="esp_id", referencedColumnName="esp_id")
     * })
     */
    private $esp;


}
