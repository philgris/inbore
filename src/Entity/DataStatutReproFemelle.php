<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataStatutReproFemelle
 *
 * @ORM\Table(name="data_statut_repro_femelle")
 * @ORM\Entity
 */
class DataStatutReproFemelle
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
     * @ORM\Column(name="staref_poids_gonade_droite", type="float", precision=10, scale=0, nullable=true)
     */
    private $starefPoidsGonadeDroite;

    /**
     * @var float|null
     *
     * @ORM\Column(name="staref_poids_gonade_gauche", type="float", precision=10, scale=0, nullable=true)
     */
    private $starefPoidsGonadeGauche;

    /**
     * @var float|null
     *
     * @ORM\Column(name="staref_dimension_gonade_droite", type="float", precision=10, scale=0, nullable=true)
     */
    private $starefDimensionGonadeDroite;

    /**
     * @var float|null
     *
     * @ORM\Column(name="staref_dimension_gonade_gauche", type="float", precision=10, scale=0, nullable=true)
     */
    private $starefDimensionGonadeGauche;

    /**
     * @var float|null
     *
     * @ORM\Column(name="staref_taille_cl", type="float", precision=10, scale=0, nullable=true)
     */
    private $starefTailleCl;

    /**
     * @var int|null
     *
     * @ORM\Column(name="staref_n_ca_od", type="integer", nullable=true)
     */
    private $starefNCaOd;

    /**
     * @var int|null
     *
     * @ORM\Column(name="staref_n_ca_og", type="integer", nullable=true)
     */
    private $starefNCaOg;

    /**
     * @var string|null
     *
     * @ORM\Column(name="poscl_code", type="string", length=50, nullable=true)
     */
    private $posclCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="statrepro_code", type="string", length=50, nullable=true)
     */
    private $statreproCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fol_libelle", type="string", length=50, nullable=true)
     */
    private $folLibelle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_import", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateImport = 'CURRENT_TIMESTAMP';


}
