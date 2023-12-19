<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LotPrelevement
 *
 * @ORM\Table(name="lot_prelevement", indexes={@ORM\Index(name="fk_LOT_PRELEVEMENT_MAMMIFERE_INDIVIDU1", columns={"id_mammifere"})})
 * @ORM\Entity
 */
class LotPrelevement
{
    /**
     * @var int
     *
     * @ORM\Column(name="idlot_prelevement", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idlotPrelevement;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="congel_date", type="date", nullable=true)
     */
    private $congelDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="congel_num", type="string", length=50, nullable=true)
     */
    private $congelNum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="congel_lieu", type="string", length=255, nullable=true)
     */
    private $congelLieu;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="congel_intoto", type="boolean", nullable=true)
     */
    private $congelIntoto = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="transfert_date", type="date", nullable=true)
     */
    private $transfertDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="transfert_operateur", type="string", length=255, nullable=true)
     */
    private $transfertOperateur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaires", type="text", length=65535, nullable=true)
     */
    private $commentaires;

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
     * @var \MammifereIndividu
     *
     * @ORM\ManyToOne(targetEntity="MammifereIndividu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_mammifere", referencedColumnName="idmammifere")
     * })
     */
    private $idMammifere;


}
