<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * ArticleBlog
 *
 * @ORM\Table(name="articleBlog")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleBlogRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ArticleBlog
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="articles" )
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\NotBlank()
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published", type="datetime", nullable=true)
     */
    private $published = null;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\Column(name="file", type="string", nullable=true)
     * @Assert\File(mimeTypes={ "image/jpeg", "image/png", "image/jpg"}, maxSize="8M")
     */
    private $file;

   


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $deleted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEvenement", type="datetime", nullable=true)
     */
    private $dateEvenement = null;
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param \DateTime $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    /**
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param \DateTime $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }
   
    /**
     * @return \DateTime
     */
    public function getDateEvenement()
    {
        return $this->dateEvenement;
    }

    /**
     * @param \DateTime $dateEvenement
     */
    public function setDateEvenement($dateEvenement)
    {
        $this->dateEvenement = $dateEvenement;
    }

    /**
     * @return UploadedFile|string
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }
}
