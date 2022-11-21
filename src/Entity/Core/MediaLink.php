<?php

namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * MediaLink
 *
 * @ORM\Table(name="media_link")
 * @ORM\Entity
 */
class MediaLink
{
    /**
     * @var int
     *
     * @ORM\Column(name="idmedia_link", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="media_link_idmedia_link_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datecre", type="datetime", nullable=true)
     */
    private $dateCre;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datemaj", type="datetime", nullable=true)
     */
    private $dateMaj;

    /**
     * @var int|null
     *
     * @ORM\Column(name="usercre", type="integer", nullable=true)
     */
    private $userCre;

    /**
     * @var int|null
     *
     * @ORM\Column(name="usermaj", type="integer", nullable=true)
     */
    private $userMaj;

    /**
     * @var \Media
     *
     * @ORM\ManyToOne(targetEntity="Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_media", referencedColumnName="idmedia", nullable=false, onDelete="CASCADE")
     * })
     */
    private $idMedia;

    // 1 - Exemple of linked Table with associated Media 
    // /**
    //  * @var \Table
    //  *
    //  * @ORM\ManyToOne(targetEntity="Table")
    //  * @ORM\JoinColumns({
    //  *   @ORM\JoinColumn(name="id_table", referencedColumnName="idtable", onDelete="CASCADE")
    //  * })
    //  */
    // private $idTable;
 
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getUserCre(): ?int
    {
        return $this->userCre;
    }

    public function setUserCre(?int $userCre): self
    {
        $this->userCre = $userCre;

        return $this;
    }

    public function getUserMaj(): ?int
    {
        return $this->userMaj;
    }

    public function setUserMaj(?int $userMaj): self
    {
        $this->userMaj = $userMaj;

        return $this;
    }

    public function getIdMedia(): ?Media
    {
        return $this->idMedia;
    }

    public function setIdMedia(?Media $idMedia): self
    {
        $this->idMedia = $idMedia;

        return $this;
    }


}
