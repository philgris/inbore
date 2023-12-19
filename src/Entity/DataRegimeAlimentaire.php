<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataRegimeAlimentaire
 *
 * @ORM\Table(name="data_regime_alimentaire")
 * @ORM\Entity
 */
class DataRegimeAlimentaire
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
     * @var int|null
     *
     * @ORM\Column(name="preest_masse_totale_g", type="integer", nullable=true)
     */
    private $preestMasseTotaleG;

    /**
     * @var int|null
     *
     * @ORM\Column(name="preest_masse_paroie_g", type="integer", nullable=true)
     */
    private $preestMasseParoieG;

    /**
     * @var int|null
     *
     * @ORM\Column(name="preest_taux_repletion", type="integer", nullable=true)
     */
    private $preestTauxRepletion;

    /**
     * @var float|null
     *
     * @ORM\Column(name="preest_pourc_parasites", type="float", precision=10, scale=0, nullable=true)
     */
    private $preestPourcParasites;

    /**
     * @var float|null
     *
     * @ORM\Column(name="preest_pourc_poisson", type="float", precision=10, scale=0, nullable=true)
     */
    private $preestPourcPoisson;

    /**
     * @var float|null
     *
     * @ORM\Column(name="preest_pourc_cephalopodes", type="float", precision=10, scale=0, nullable=true)
     */
    private $preestPourcCephalopodes;

    /**
     * @var float|null
     *
     * @ORM\Column(name="preest_pourc_crustaces", type="float", precision=10, scale=0, nullable=true)
     */
    private $preestPourcCrustaces;

    /**
     * @var int|null
     *
     * @ORM\Column(name="preest_taux_parasitisme", type="integer", nullable=true)
     */
    private $preestTauxParasitisme;

    /**
     * @var string|null
     *
     * @ORM\Column(name="preest_remarques", type="text", length=65535, nullable=true)
     */
    private $preestRemarques;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_import", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateImport = 'CURRENT_TIMESTAMP';


}
