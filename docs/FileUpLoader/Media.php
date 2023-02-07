<?php

namespace App\Entity;

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



}
