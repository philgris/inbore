<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Typecontactvoc
 *
 * @ORM\Table(name="typecontactvoc", indexes={@ORM\Index(name="fki_contact_fk_fkey", columns={"contact_fk"}), @ORM\Index(name="fki_voc_fk_fkey", columns={"voc_fk"})})
 * @ORM\Entity
 */
class Typecontactvoc
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="typecontactvoc_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Contact
     *
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="typecontactvocs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $contactFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="voc_fk", referencedColumnName="id")
     * })
     */
    private $vocFk;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContactFk(): ?Contact
    {
        return $this->contactFk;
    }

    public function setContactFk(?Contact $contactFk): self
    {
        $this->contactFk = $contactFk;

        return $this;
    }

    public function getVocFk(): ?Voc
    {
        return $this->vocFk;
    }

    public function setVocFk(?Voc $vocFk): self
    {
        $this->vocFk = $vocFk;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getListedescontactsFk(): ?Contact
    {
        return $this->listedescontactsFk;
    }

    public function setListedescontactsFk(?Contact $listedescontactsFk): self
    {
        $this->listedescontactsFk = $listedescontactsFk;

        return $this;
    }


}
