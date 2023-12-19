<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConclusionAutopsie
 *
 * @ORM\Table(name="conclusion_autopsie", indexes={@ORM\Index(name="conclusion_autopsie", columns={"idconclusion_autopsie"})})
 * @ORM\Entity
 */
class ConclusionAutopsie
{
    /**
     * @var int
     *
     * @ORM\Column(name="idconclusion_autopsie", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idconclusionAutopsie;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ca_libelle", type="string", length=255, nullable=true)
     */
    private $caLibelle;

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
