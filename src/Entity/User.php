<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @method string getUserIdentifier()
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @Vich\Uploadable
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $age;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $pays;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $ville;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $city;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $profil_picture;

    /**
     * @Vich\UploadableField(mapping="profile_picture")
     *
     */
    public $editPicture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $rayon_action;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $note;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $sitter_verifie;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $tarif;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Pet::class, mappedBy="user")
     */
    private $animaux;

    /**
     * @ORM\OneToMany(targetEntity=Planning::class, mappedBy="sitter")
     */
    private $plannings;


    public function __construct()
    {
        $this->animaux = new ArrayCollection();
        $this->plannings = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEditPicture()
    {
        return $this->editPicture;
    }

    /**
     * @param mixed $editPicture
     */
    public function setEditPicture(File $editPicture = null): void
    {
        $this->editPicture = $editPicture;
    }

    public function getCity(): ?int
    {
        return $this->city;
    }

    public function setCity(int $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getProfilPicture(): ?string
    {
        return $this->profil_picture;
    }

    public function setProfilPicture(?string $profil_picture): self
    {
        $this->profil_picture = $profil_picture;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRayonAction(): ?int
    {
        return $this->rayon_action;
    }

    public function setRayonAction(?int $rayon_action): self
    {
        $this->rayon_action = $rayon_action;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getSitterVerifie(): ?bool
    {
        return $this->sitter_verifie;
    }

    public function setSitterVerifie(?bool $sitter_verifie): self
    {
        $this->sitter_verifie = $sitter_verifie;

        return $this;
    }

    public function getTarif(): ?int
    {
        return $this->tarif;
    }

    public function setTarif(?int $tarif): self
    {
        $this->tarif = $tarif;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->password,
            $this->profil_picture,
            $this->nom,
            $this->prenom,
            $this->adresse,
            $this->age,
            $this->rayon_action,
            $this->username,
            $this->email,
            $this->description,
            $this->ville,
            $this->city
        ));
    }

    public function unserialize($data)
    {
        list (
            $this->id,
            $this->password,
            $this->profil_picture,
            $this->nom,
            $this->prenom,
            $this->adresse,
            $this->age,
            $this->rayon_action,
            $this->username,
            $this->email,
            $this->description,
            $this->ville,
            $this->city
            ) = unserialize($data, array('allowed_classes' => false));
    }

    /**
     * @return Collection|Pet[]
     */
    public function getAnimaux(): Collection
    {
        return $this->animaux;
    }

    public function addAnimaux(Pet $animaux): self
    {
        if (!$this->animaux->contains($animaux)) {
            $this->animaux[] = $animaux;
            $animaux->setUser($this);
        }

        return $this;
    }

    public function removeAnimaux(Pet $animaux): self
    {
        if ($this->animaux->removeElement($animaux)) {
            // set the owning side to null (unless already changed)
            if ($animaux->getUser() === $this) {
                $animaux->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Planning[]
     */
    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings[] = $planning;
            $planning->setSitter($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getSitter() === $this) {
                $planning->setSitter(null);
            }
        }

        return $this;
    }




}
