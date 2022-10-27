<?php

namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="App\Repository\Core\MediaRepository")
 */
class Media
{
    /**
     * @var int
     *
     * @ORM\Column(name="idmedia", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="media_idmedia_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var string|null
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mime_type", type="string", length=255, nullable=true)
     */
    private $mimeType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="size", type="bigint", nullable=true)
     */
    private $size;

    /**
     * @var int|null
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width;

    /**
     * @var int|null
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var string|null
     *
     * @ORM\Column(name="credit", type="string", length=255, nullable=true)
     */
    private $credit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="license", type="string", length=50, nullable=true)
     */
    private $license;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uri_old_file", type="string", length=50, nullable=true)
     */
    private $uriOldFile;

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
     * @var int|null
     *
     * @ORM\Column(name="useradmin", type="integer", nullable=true)
     */
    private $userAdmin;

    /**
     * @var int|null
     *
     * @ORM\Column(name="groupadmin", type="integer", nullable=true)
     */
    private $groupAdmin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getCredit(): ?string
    {
        return $this->credit;
    }

    public function setCredit(?string $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(?string $license): self
    {
        $this->license = $license;

        return $this;
    }

    public function getUriOldFile(): ?string
    {
        return $this->uriOldFile;
    }

    public function setUriOldFile(?string $uriOldFile): self
    {
        $this->uriOldFile = $uriOldFile;

        return $this;
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

    public function getUserAdmin(): ?int
    {
        return $this->userAdmin;
    }

    public function setUserAdmin(?int $userAdmin): self
    {
        $this->userAdmin = $userAdmin;

        return $this;
    }

    public function getGroupAdmin(): ?int
    {
        return $this->groupAdmin;
    }

    public function setGroupAdmin(?int $groupAdmin): self
    {
        $this->groupAdmin = $groupAdmin;

        return $this;
    }


}
