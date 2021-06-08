<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adress
 *
 * @ORM\Table(name="adress")
 * @ORM\Entity(repositoryClass="App\Repository\Core\AdressRepository")
 * 
 */
class Adress
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="adress_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     */
    private $number;

    /**
     * @var string|null
     *
     * @ORM\Column(name="postal_code", type="string", length=255, nullable=true)
     */
    private $postalCode;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_user_cre", type="datetime", nullable=true)
     */
    private $dateCre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_user_maj", type="datetime", nullable=true)
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


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

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
