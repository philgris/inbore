<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataStatutReproMale
 *
 * @ORM\Table(name="data_statut_repro_male")
 * @ORM\Entity
 */
class DataStatutReproMale
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
     * @var float|null
     *
     * @ORM\Column(name="starem_poids_test_droit", type="float", precision=10, scale=0, nullable=true)
     */
    private $staremPoidsTestDroit;

    /**
     * @var float|null
     *
     * @ORM\Column(name="starem_poids_test_gauche", type="float", precision=10, scale=0, nullable=true)
     */
    private $staremPoidsTestGauche;

    /**
     * @var float|null
     *
     * @ORM\Column(name="starem_dimension_test_droit", type="float", precision=10, scale=0, nullable=true)
     */
    private $staremDimensionTestDroit;

    /**
     * @var float|null
     *
     * @ORM\Column(name="starem_dimension_test_gauche", type="float", precision=10, scale=0, nullable=true)
     */
    private $staremDimensionTestGauche;

    /**
     * @var string|null
     *
     * @ORM\Column(name="statrepro_code", type="string", length=50, nullable=true)
     */
    private $statreproCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_import", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateImport = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="code_sexe", type="integer", nullable=false)
     */
    private $codeSexe;


}
