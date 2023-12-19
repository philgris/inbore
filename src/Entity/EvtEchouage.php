<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EvtEchouage
 *
 * @ORM\Table(name="evt_echouage", indexes={@ORM\Index(name="fk_EVT_ECHOUAGE_com_codes_insee", columns={"com_codes_insee"})})
 * @ORM\Entity
 */
class EvtEchouage
{
    /**
     * @var int
     *
     * @ORM\Column(name="idechouage", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idechouage;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_obs", type="date", nullable=true)
     */
    private $dateObs;

    /**
     * @var string|null
     *
     * @ORM\Column(name="localisation", type="string", length=255, nullable=true)
     */
    private $localisation;

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
     * @ORM\Column(name="commentaires", type="text", length=65535, nullable=true)
     */
    private $commentaires;

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
     * @var \LieuCommune
     *
     * @ORM\ManyToOne(targetEntity="LieuCommune")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="com_codes_insee", referencedColumnName="idlieu_commune")
     * })
     */
    private $comCodesInsee;


}
