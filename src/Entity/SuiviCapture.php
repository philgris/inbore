<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SuiviCapture
 *
 * @ORM\Table(name="suivi_capture", indexes={@ORM\Index(name="fk_SUIVI_CAPTURE_id_mammifere", columns={"id_mammifere"}), @ORM\Index(name="fk_SUIVI_CAPTURE_id_ncc", columns={"id_ncc"}), @ORM\Index(name="fk_SUIVI_CAPTURE_CODE_DEVENIR1", columns={"code_devenir"}), @ORM\Index(name="fk_SUIVI_CAPTURE_id_capture", columns={"id_capture"})})
 * @ORM\Entity
 */
class SuiviCapture
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
     * @var string|null
     *
     * @ORM\Column(name="code_etat", type="string", length=0, nullable=true)
     */
    private $codeEtat;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="bague_date", type="date", nullable=true)
     */
    private $bagueDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="bague_heure", type="time", nullable=true)
     */
    private $bagueHeure;

    /**
     * @var float|null
     *
     * @ORM\Column(name="bague_latitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $bagueLatitude;

    /**
     * @var float|null
     *
     * @ORM\Column(name="bague_longitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $bagueLongitude;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code_observateur", type="string", length=50, nullable=true)
     */
    private $codeObservateur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code_capture", type="string", length=255, nullable=true)
     */
    private $codeCapture;

    /**
     * @var string|null
     *
     * @ORM\Column(name="info_photo", type="text", length=65535, nullable=true)
     */
    private $infoPhoto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaires_suivi_capture", type="text", length=65535, nullable=true)
     */
    private $commentairesSuiviCapture;

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
     * @var \CodeDevenir
     *
     * @ORM\ManyToOne(targetEntity="CodeDevenir")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_devenir", referencedColumnName="idcode_devenir")
     * })
     */
    private $codeDevenir;

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
     * @var \EvtCapture
     *
     * @ORM\ManyToOne(targetEntity="EvtCapture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_capture", referencedColumnName="idcapture")
     * })
     */
    private $idCapture;

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
