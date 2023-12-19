<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Taxon
 *
 * @ORM\Table(name="taxon", indexes={@ORM\Index(name="fk_TAXON_famille", columns={"famille"}), @ORM\Index(name="fk_TAXON_lb_nom", columns={"lb_nom"}), @ORM\Index(name="fk_TAXON_ordre", columns={"ordre"})})
 * @ORM\Entity
 */
class Taxon
{
    /**
     * @var int
     *
     * @ORM\Column(name="esp_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $espId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phylum", type="string", length=50, nullable=true)
     */
    private $phylum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="classe", type="string", length=50, nullable=true)
     */
    private $classe;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ordre", type="string", length=50, nullable=true)
     */
    private $ordre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="super_famille", type="string", length=50, nullable=true)
     */
    private $superFamille;

    /**
     * @var string|null
     *
     * @ORM\Column(name="famille", type="string", length=50, nullable=true)
     */
    private $famille;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cd_nom", type="integer", nullable=true)
     */
    private $cdNom;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cd_taxsup", type="integer", nullable=true)
     */
    private $cdTaxsup;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cd_ref", type="integer", nullable=true)
     */
    private $cdRef;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rang", type="string", length=50, nullable=true)
     */
    private $rang;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_worms", type="integer", nullable=true)
     */
    private $idWorms;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lb_nom", type="string", length=50, nullable=true)
     */
    private $lbNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_comm", type="string", length=50, nullable=true)
     */
    private $nomComm;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_angl", type="string", length=50, nullable=true)
     */
    private $nomAngl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=50, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="groupe", type="string", length=50, nullable=true)
     */
    private $groupe;

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
