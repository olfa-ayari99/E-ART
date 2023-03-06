<?php

namespace App\Entity;

class Personne
{

    private $cin;
    private $nom;
    private $prenom;
    private $hopital_id;


    public function __construct($hopital){
        $this->hopital_id = $hopital;
    }

    /**
     * @return mixed
     */
    public function getHopitalId()
    {
        return $this->hopital_id;
    }

    /**
     * @param mixed $hopital_id
     */
    public function setHopitalId($hopital_id): void
    {
        $this->hopital_id = $hopital_id;
    }



    /**
     * @return mixed
     */
    public function getCin()
    {
        return $this->cin;
    }

    /**
     * @param mixed $cin
     */
    public function setCin($cin): void
    {
        $this->cin = $cin;
    }

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
    public function setNom($nom): void
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
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }



}