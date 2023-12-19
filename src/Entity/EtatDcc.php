<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EtatDcc
 *
 * @ORM\Table(name="etat_dcc")
 * @ORM\Entity
 */
class EtatDcc
{
    /**
     * @var int
     *
     * @ORM\Column(name="idetat_dcc", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idetatDcc;

    /**
     * @var string
     *
     * @ORM\Column(name="etat_libelle", type="string", length=50, nullable=false)
     */
    private $etatLibelle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code_court", type="text", length=255, nullable=true)
     */
    private $codeCourt;

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
