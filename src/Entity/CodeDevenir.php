<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CodeDevenir
 *
 * @ORM\Table(name="code_devenir", indexes={@ORM\Index(name="code_libelle", columns={"cd_libelle"})})
 * @ORM\Entity
 */
class CodeDevenir
{
    /**
     * @var int
     *
     * @ORM\Column(name="idcode_devenir", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcodeDevenir;

    /**
     * @var string
     *
     * @ORM\Column(name="cd_libelle", type="string", length=50, nullable=false)
     */
    private $cdLibelle;

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
