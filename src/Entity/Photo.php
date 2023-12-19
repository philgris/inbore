<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Photo
 *
 * @ORM\Table(name="photo", indexes={@ORM\Index(name="fk_PHOTO_SUIVI_CAPTURE2", columns={"id_suivi_capture"}), @ORM\Index(name="fk_PHOTO_DISSECTION1", columns={"id_dissection"}), @ORM\Index(name="fk_PHOTO_EVT_ECHOUAGE2", columns={"id_evt_echouage"}), @ORM\Index(name="fk_PHOTO_EVT_CAPTURE2", columns={"id_evt_capture"}), @ORM\Index(name="fk_PHOTO_SUIVI_ECHOUAGE2", columns={"id_suivi_echouage"}), @ORM\Index(name="fk_PHOTO_MAMMIFERE_INDIVIDU3", columns={"id_mammifere_individu"})})
 * @ORM\Entity
 */
class Photo
{
    /**
     * @var int
     *
     * @ORM\Column(name="idphoto", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idphoto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=50, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="libelle", type="string", length=50, nullable=true)
     */
    private $libelle;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_time", type="datetime", nullable=true)
     */
    private $dateTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mots_cles", type="string", length=255, nullable=true)
     */
    private $motsCles;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cr", type="string", length=255, nullable=true)
     */
    private $cr;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_prelevement", type="integer", nullable=true)
     */
    private $idPrelevement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uri_raw", type="string", length=255, nullable=true)
     */
    private $uriRaw;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uri_thumb", type="string", length=255, nullable=true)
     */
    private $uriThumb;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uri_resize", type="string", length=255, nullable=true)
     */
    private $uriResize;

    /**
     * @var int|null
     *
     * @ORM\Column(name="phototeque_id", type="integer", nullable=true)
     */
    private $phototequeId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="qualite", type="string", length=0, nullable=true)
     */
    private $qualite;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=true)
     */
    private $comment;

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
     * @var \EvtEchouage
     *
     * @ORM\ManyToOne(targetEntity="EvtEchouage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_evt_echouage", referencedColumnName="idechouage")
     * })
     */
    private $idEvtEchouage;

    /**
     * @var \SuiviEchouage
     *
     * @ORM\ManyToOne(targetEntity="SuiviEchouage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_suivi_echouage", referencedColumnName="id")
     * })
     */
    private $idSuiviEchouage;

    /**
     * @var \Dissection
     *
     * @ORM\ManyToOne(targetEntity="Dissection")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_dissection", referencedColumnName="iddissection")
     * })
     */
    private $idDissection;

    /**
     * @var \MammifereIndividu
     *
     * @ORM\ManyToOne(targetEntity="MammifereIndividu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_mammifere_individu", referencedColumnName="idmammifere")
     * })
     */
    private $idMammifereIndividu;

    /**
     * @var \EvtCapture
     *
     * @ORM\ManyToOne(targetEntity="EvtCapture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_evt_capture", referencedColumnName="idcapture")
     * })
     */
    private $idEvtCapture;

    /**
     * @var \SuiviCapture
     *
     * @ORM\ManyToOne(targetEntity="SuiviCapture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_suivi_capture", referencedColumnName="id")
     * })
     */
    private $idSuiviCapture;


}
