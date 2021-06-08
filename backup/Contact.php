<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table(name="contact", indexes={@ORM\Index(name="idx_4c62e638adb59ad6", columns={"adress_fk"})})
 * 
 */
class Contact
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="contact_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="no", type="string", length=255, nullable=true)
     */
    private $no;

    /**
     * @var \Adress
     *
     * @ORM\ManyToOne(targetEntity="Adress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="adress_fk", referencedColumnName="id")
     * })
     */
    private $adressFk;
    
    /**
     * @ORM\OneToMany(targetEntity=Typecontactvoc::class, mappedBy="contactFk", cascade={"persist"})
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $typecontactvocs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_cre", type="datetime", nullable=true)
     */
    private $dateCre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_maj", type="datetime", nullable=true)
     */
    private $dateMaj;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_cre", type="bigint", nullable=true)
     */
    private $userCre;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_maj", type="bigint", nullable=true)
     */
    private $userMaj;


    public function __construct()
    {
        $this->typecontactvocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNo(): ?string
    {
        return $this->no;
    }

    public function setNo(?string $no): self
    {
        $this->no = $no;

        return $this;
    }

    public function getAdressFk(): ?Adress
    {
        return $this->adressFk;
    }

    public function setAdressFk(?Adress $adressFk): self
    {
        $this->adressFk = $adressFk;

        return $this;
    }

    /**
     * @return Collection|Typecontactvoc[]
     */
    public function getTypecontactvocs(): Collection
    {
        return $this->typecontactvocs;
    }

    public function addTypecontactvoc(Typecontactvoc $typecontactvoc): self
    {
        if (!$this->typecontactvocs->contains($typecontactvoc)) {
            $this->typecontactvocs[] = $typecontactvoc;
            $typecontactvoc->setContactFk($this);
        }

        return $this;
    }

    public function removeTypecontactvoc(Typecontactvoc $typecontactvoc): self
    {
        if ($this->typecontactvocs->removeElement($typecontactvoc)) {
            // set the owning side to null (unless already changed)
            if ($typecontactvoc->getContactFk() === $this) {
                $typecontactvoc->setContactFk(null);
            }
        }

        return $this;
    }

    public function getDateCre(): ?\DateTimeInterface
    {
        return $this->dateCre;
    }

    public function setDateCre(?\DateTimeInterface $dateCre): self
    {
        $this->dateCre = $dateCre;

        return $this;
    }

    public function getDateMaj(): ?\DateTimeInterface
    {
        return $this->dateMaj;
    }

    public function setDateMaj(?\DateTimeInterface $dateMaj): self
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }

    public function getUserCre(): ?string
    {
        return $this->userCre;
    }

    public function setUserCre(?string $userCre): self
    {
        $this->userCre = $userCre;

        return $this;
    }

    public function getUserMaj(): ?string
    {
        return $this->userMaj;
    }

    public function setUserMaj(?string $userMaj): self
    {
        $this->userMaj = $userMaj;

        return $this;
    }


}
