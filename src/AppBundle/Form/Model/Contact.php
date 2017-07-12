<?php

namespace AppBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;

class Contact
{
    /**
     * @Assert\NotBlank(message="Ce champs ne doit pas être vide")
     */
    private $nom;

    /**
     * @Assert\NotBlank(message="Ce champs ne doit pas être vide")
     */
    private $prenom;

    /**
     * @Assert\Email(message="Veuillez entrer une adresse email valide")
     * @Assert\NotBlank(message="Ce champs ne doit pas être vide")
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Ce champs ne doit pas être vide")
     */
    private $contenu;

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * @param mixed $contenu
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

}
