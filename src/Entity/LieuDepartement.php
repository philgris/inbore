<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LieuDepartement
 *
 * @ORM\Table(name="lieu_departement", indexes={@ORM\Index(name="code_zone_index", columns={"code_zone"})})
 * @ORM\Entity
 */
class LieuDepartement
{
    /**
     * @var string
     *
     * @ORM\Column(name="dept_code", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $deptCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dept_nom", type="string", length=50, nullable=true)
     */
    private $deptNom;

    /**
     * @var float|null
     *
     * @ORM\Column(name="dept_superficie_km2", type="float", precision=10, scale=0, nullable=true)
     */
    private $deptSuperficieKm2 = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="reg_nom", type="string", length=50, nullable=true)
     */
    private $regNom;

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


}
