<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prelevement
 *
 * @ORM\Table(name="prelevement", indexes={@ORM\Index(name="fk_PRELEVEMENT_id_type_conditionnement", columns={"id_type_conditionnement"}), @ORM\Index(name="fk_PRELEVEMENT_LIEU_STOCKAGE1", columns={"id_lieu_stockage"}), @ORM\Index(name="fk_id_dissection", columns={"id_dissection"}), @ORM\Index(name="fk_PRELEVEMENT_id_type_prelevement", columns={"id_type_prelevement"})})
 * @ORM\Entity
 */
class Prelevement
{
    /**
     * @var int
     *
     * @ORM\Column(name="idprelevement", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idprelevement;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="pathologie", type="boolean", nullable=true)
     */
    private $pathologie = '0';

    /**
     * @var float|null
     *
     * @ORM\Column(name="quantite", type="float", precision=10, scale=0, nullable=true)
     */
    private $quantite;

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
     * @var \TypeConditionement
     *
     * @ORM\ManyToOne(targetEntity="TypeConditionement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_type_conditionnement", referencedColumnName="idtype_conditionnement")
     * })
     */
    private $idTypeConditionnement;

    /**
     * @var \Dissection
     *
     * @ORM\ManyToOne(targetEntity="Dissection")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_dissection", referencedColumnName="iddissection")
     * })
     */
    private $idDissection;

    /**
     * @var \TypePrelevement
     *
     * @ORM\ManyToOne(targetEntity="TypePrelevement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_type_prelevement", referencedColumnName="idtype_prelevement")
     * })
     */
    private $idTypePrelevement;

    /**
     * @var \LieuStockage
     *
     * @ORM\ManyToOne(targetEntity="LieuStockage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_lieu_stockage", referencedColumnName="idlieu_stockage")
     * })
     */
    private $idLieuStockage;


}
