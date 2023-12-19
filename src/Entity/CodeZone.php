<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CodeZone
 *
 * @ORM\Table(name="code_zone")
 * @ORM\Entity
 */
class CodeZone
{
    /**
     * @var string
     *
     * @ORM\Column(name="idcode_zone", type="string", length=5, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcodeZone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cz_libelle", type="string", length=50, nullable=true)
     */
    private $czLibelle;

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


}