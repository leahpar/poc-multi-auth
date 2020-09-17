<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    public $email;

    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Casquette::class, mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $casquettes;

    /**
     * @var Casquette
     */
    private $activeCasquette;

    public function __construct()
    {
        $this->casquettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        if (!$this->roles) {
            // Every user has at least ROLE_USER
            $roles = ['ROLE_USER'];

            $rolesCasquette = $this->activeCasquette ? $this->activeCasquette->getRoles() : [];
            $this->roles = array_unique(array_merge($roles, $rolesCasquette));
        }
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Casquette[]
     */
    public function getCasquettes(): Collection
    {
        return $this->casquettes;
    }

    public function addCasquette(Casquette $casquette): self
    {
        if (!$this->casquettes->contains($casquette)) {
            $this->casquettes[] = $casquette;
            $casquette->setUser($this);
        }

        return $this;
    }

    public function removeCasquette(Casquette $casquette): self
    {
        if ($this->casquettes->contains($casquette)) {
            $this->casquettes->removeElement($casquette);
            // set the owning side to null (unless already changed)
            if ($casquette->getUser() === $this) {
                $casquette->setUser(null);
            }
        }

        return $this;
    }

    public function getActiveCasquette(): Casquette
    {
        return $this->activeCasquette;
    }

    public function setActiveCasquette(Casquette $activeCasquette): self
    {
        $this->activeCasquette = $activeCasquette;

        return $this;
    }

}
