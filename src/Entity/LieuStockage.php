<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LieuStockage
 *
 * @ORM\Table(name="lieu_stockage")
 * @ORM\Entity
 */
class LieuStockage
{
    /**
     * @var int
     *
     * @ORM\Column(name="idlieu_stockage", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idlieuStockage;

    /**
     * @var string
     *
     * @ORM\Column(name="lieu_stockage", type="string", length=0, nullable=false)
     */
    private $lieuStockage;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rack", type="string", length=0, nullable=true)
     */
    private $rack;

    /**
     * @var string|null
     *
     * @ORM\Column(name="colonne", type="string", length=0, nullable=true)
     */
    private $colonne;

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
