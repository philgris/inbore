<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EnginEspeceCible
 *
 * @ORM\Table(name="engin_espece_cible")
 * @ORM\Entity
 */
class EnginEspeceCible
{
    /**
     * @var int
     *
     * @ORM\Column(name="idengin_espece_cible", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idenginEspeceCible;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_libelle", type="string", length=50, nullable=false)
     */
    private $eecLibelle;

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
