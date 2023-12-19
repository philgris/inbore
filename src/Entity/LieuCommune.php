<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LieuCommune
 *
 * @ORM\Table(name="lieu_commune", indexes={@ORM\Index(name="fk_LIEU_COMMUNE_dept_code", columns={"dept_code"})})
 * @ORM\Entity
 */
class LieuCommune
{
    /**
     * @var string
     *
     * @ORM\Column(name="idlieu_commune", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idlieuCommune;

    /**
     * @var string|null
     *
     * @ORM\Column(name="com_nom", type="string", length=50, nullable=true)
     */
    private $comNom;

    /**
     * @var float|null
     *
     * @ORM\Column(name="com_coord_x", type="float", precision=10, scale=0, nullable=true)
     */
    private $comCoordX;

    /**
     * @var float|null
     *
     * @ORM\Column(name="com_coord_y", type="float", precision=10, scale=0, nullable=true)
     */
    private $comCoordY;

    /**
     * @var float|null
     *
     * @ORM\Column(name="com_coord_lat", type="float", precision=10, scale=0, nullable=true)
     */
    private $comCoordLat;

    /**
     * @var float|null
     *
     * @ORM\Column(name="com_coord_long", type="float", precision=10, scale=0, nullable=true)
     */
    private $comCoordLong;

    /**
     * @var int|null
     *
     * @ORM\Column(name="com_superficie", type="integer", nullable=true)
     */
    private $comSuperficie;

    /**
     * @var string|null
     *
     * @ORM\Column(name="com_littorale", type="string", length=1, nullable=true)
     */
    private $comLittorale;

    /**
     * @var int|null
     *
     * @ORM\Column(name="com_km_littoral", type="integer", nullable=true)
     */
    private $comKmLittoral;

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
     * @var \LieuDepartement
     *
     * @ORM\ManyToOne(targetEntity="LieuDepartement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dept_code", referencedColumnName="dept_code")
     * })
     */
    private $deptCode;


}
