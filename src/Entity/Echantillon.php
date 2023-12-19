<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Echantillon
 *
 * @ORM\Table(name="echantillon", indexes={@ORM\Index(name="fk_ECHANTILLON_id_prelevement", columns={"id_prelevement"}), @ORM\Index(name="fk_ECHANTILLON_id_envoi", columns={"id_envoi"})})
 * @ORM\Entity
 */
class Echantillon
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float|null
     *
     * @ORM\Column(name="quantite_envoi", type="float", precision=10, scale=0, nullable=true)
     */
    private $quantiteEnvoi;

    /**
     * @var float|null
     *
     * @ORM\Column(name="quantite_retour", type="float", precision=10, scale=0, nullable=true)
     */
    private $quantiteRetour;

    /**
     * @var string|null
     *
     * @ORM\Column(name="produit_final_retour", type="string", length=50, nullable=true)
     */
    private $produitFinalRetour;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="prelevement_retour", type="boolean", nullable=true)
     */
    private $prelevementRetour = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaire_echantillon", type="text", length=65535, nullable=true)
     */
    private $commentaireEchantillon;

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
     * @var \Envoi
     *
     * @ORM\ManyToOne(targetEntity="Envoi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_envoi", referencedColumnName="idenvoi")
     * })
     */
    private $idEnvoi;

    /**
     * @var \Prelevement
     *
     * @ORM\ManyToOne(targetEntity="Prelevement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_prelevement", referencedColumnName="idprelevement")
     * })
     */
    private $idPrelevement;


}
