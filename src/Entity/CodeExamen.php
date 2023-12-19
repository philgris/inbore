<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CodeExamen
 *
 * @ORM\Table(name="code_examen")
 * @ORM\Entity
 */
class CodeExamen
{
    /**
     * @var int
     *
     * @ORM\Column(name="idcode_examen", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcodeExamen;

    /**
     * @var string
     *
     * @ORM\Column(name="ce_libelle", type="string", length=50, nullable=false)
     */
    private $ceLibelle;

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
