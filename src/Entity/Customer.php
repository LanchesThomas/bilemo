<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getUsers"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUsers"])]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUsers"])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getUsers"])]
    private ?string $password = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'customer', orphanRemoval: true)]
    private Collection $Users;

    public function __construct()
    {
        $this->Users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        // Tu peux adapter si tu veux gérer des rôles en base plus tard
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void {

    }


    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->Users;
    }

    public function addUser(User $user): static
    {
        if (!$this->Users->contains($user)) {
            $this->Users->add($user);
            $user->setCustomer($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->Users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCustomer() === $this) {
                $user->setCustomer(null);
            }
        }

        return $this;
    }
}
