<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CodeCapture
 *
 * @ORM\Table(name="code_capture")
 * @ORM\Entity
 */
class CodeCapture
{
    /**
     * @var int
     *
     * @ORM\Column(name="idcode_capture", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcodeCapture;

    /**
     * @var string
     *
     * @ORM\Column(name="cc_libelle", type="string", length=255, nullable=false)
     */
    private $ccLibelle;

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
