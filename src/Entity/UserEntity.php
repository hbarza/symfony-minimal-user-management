<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserEntityRepository")
 */
class UserEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $update_at;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserGroup", mappedBy="users")
     */
    private $user_groups;

    public function __construct()
    {
        $this->user_groups = new ArrayCollection();
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->update_at;
    }

    public function setUpdateAt(\DateTimeInterface $update_at): self
    {
        $this->update_at = $update_at;

        return $this;
    }

    /**
     * @return Collection|UserGroup[]
     */
    public function getUserGroups(): Collection
    {
        return $this->user_groups;
    }

    /**
     * Assign grop to user
     */
    public function addUserGroup(UserGroup $userGroup): self
    {
        $this->user_groups[] = $userGroup;
        $userGroup->addUser($this);

        return $this;
    }

    /**
     * Remove all user_group_user_entity recoreds for current user
     */
    public function removeUserGroup(UserGroup $userGroup): self
    {
        if ($this->getID()) {
            $sql    = "delete from user_group_user_entity where user_entity_id = {$this->getID()}";
            $em     = $doctrine->getManager();
            $stmt   = $em->getConnection()->prepare($sql);
            $stmt->execute();
        }

        return $this;
    }

    /**
     * Update groups assigned to user
     */
    public function saveUserGroups(array $requestAll, $doctrine): self
    {
        if ($this->getId()) {
            $this->removeUserGroup($doctrine);
        }
        $reqeustGroups = array_values($requestAll ?? []);
        foreach ($reqeustGroups as $groupId) {
            $this->addUserGroup($doctrine->getRepository(UserGroup::class)->find($groupId));
        }
        return $this;
    }
}
