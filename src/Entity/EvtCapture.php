<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EvtCapture
 *
 * @ORM\Table(name="evt_capture", indexes={@ORM\Index(name="fk_EVT_CAPTURE_engin_type", columns={"engin_type"}), @ORM\Index(name="fk_EVT_CAPTURE_id_programme_observation", columns={"id_programme_observation"}), @ORM\Index(name="fk_EVT_CAPTURE_CODE_ZONE1", columns={"code_zone"}), @ORM\Index(name="fk_EVT_CAPTURE_code_capt", columns={"code_capt"}), @ORM\Index(name="fk_EVT_CAPTURE_id_lc_navire_port", columns={"id_lc_navire_port"}), @ORM\Index(name="fk_EVT_CAPTURE_engin_espece_cible", columns={"engin_espece_cible"}), @ORM\Index(name="fk_EVT_CAPTURE_com_code_insee", columns={"com_code_insee"})})
 * @ORM\Entity
 */
class EvtCapture
{
    /**
     * @var int
     *
     * @ORM\Column(name="idcapture", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcapture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_capture", type="date", nullable=false)
     */
    private $dateCapture;

    /**
     * @var float|null
     *
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $latitude;

    /**
     * @var float|null
     *
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $longitude;

    /**
     * @var string|null
     *
     * @ORM\Column(name="navire_immat", type="string", length=50, nullable=true)
     */
    private $navireImmat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="informateur_info", type="string", length=255, nullable=true)
     */
    private $informateurInfo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="observateur_info", type="string", length=255, nullable=true)
     */
    private $observateurInfo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="navire_nom", type="string", length=50, nullable=true)
     */
    private $navireNom;

    /**
     * @var float|null
     *
     * @ORM\Column(name="navire_long_m", type="float", precision=10, scale=0, nullable=true)
     */
    private $navireLongM;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_debut_maree", type="date", nullable=true)
     */
    private $dateDebutMaree;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="heure_virage", type="time", nullable=true)
     */
    private $heureVirage;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_observation", type="date", nullable=true)
     */
    private $dateObservation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="localisation", type="string", length=50, nullable=true)
     */
    private $localisation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code_zone_ciem", type="string", length=50, nullable=true)
     */
    private $codeZoneCiem;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sonde", type="integer", nullable=true)
     */
    private $sonde;

    /**
     * @var string|null
     *
     * @ORM\Column(name="engin_espece_contenu", type="string", length=50, nullable=true)
     */
    private $enginEspeceContenu;

    /**
     * @var int|null
     *
     * @ORM\Column(name="engin_profondeur", type="integer", nullable=true)
     */
    private $enginProfondeur;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="engin_pinger", type="boolean", nullable=true)
     */
    private $enginPinger = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="engin_pinger_type", type="string", length=50, nullable=true)
     */
    private $enginPingerType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaires", type="text", length=65535, nullable=true)
     */
    private $commentaires;

    /**
     * @var int
     *
     * @ORM\Column(name="mid", type="integer", nullable=false)
     */
    private $mid;

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
     * @var \CodeZone
     *
     * @ORM\ManyToOne(targetEntity="CodeZone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_zone", referencedColumnName="idcode_zone")
     * })
     */
    private $codeZone;

    /**
     * @var \LieuCommune
     *
     * @ORM\ManyToOne(targetEntity="LieuCommune")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_lc_navire_port", referencedColumnName="idlieu_commune")
     * })
     */
    private $idLcNavirePort;

    /**
     * @var \EnginEspeceCible
     *
     * @ORM\ManyToOne(targetEntity="EnginEspeceCible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="engin_espece_cible", referencedColumnName="idengin_espece_cible")
     * })
     */
    private $enginEspeceCible;

    /**
     * @var \LieuCommune
     *
     * @ORM\ManyToOne(targetEntity="LieuCommune")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="com_code_insee", referencedColumnName="idlieu_commune")
     * })
     */
    private $comCodeInsee;

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
     * @var \EnginType
     *
     * @ORM\ManyToOne(targetEntity="EnginType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="engin_type", referencedColumnName="idengin_type")
     * })
     */
    private $enginType;

    /**
     * @var \ProgrammeObservation
     *
     * @ORM\ManyToOne(targetEntity="ProgrammeObservation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_programme_observation", referencedColumnName="idprogramme_observation")
     * })
     */
    private $idProgrammeObservation;


}
