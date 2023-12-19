<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataAge
 *
 * @ORM\Table(name="data_age")
 * @ORM\Entity
 */
class DataAge
{
    /**
     * @var string
     *
     * @ORM\Column(name="id_metadata", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMetadata;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_coupe", type="string", length=255, nullable=true)
     */
    private $descriptionCoupe;

    /**
     * @var float|null
     *
     * @ORM\Column(name="age_sd", type="float", precision=10, scale=0, nullable=true)
     */
    private $ageSd;

    /**
     * @var float|null
     *
     * @ORM\Column(name="age_lu", type="float", precision=10, scale=0, nullable=true)
     */
    private $ageLu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dentcond_libelle", type="string", length=50, nullable=true)
     */
    private $dentcondLibelle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="stock", type="string", length=50, nullable=true)
     */
    private $stock;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_import", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateImport = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="agrement", type="integer", nullable=false)
     */
    private $agrement;


}
