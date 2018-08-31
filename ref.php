<?php

namespace App\Entity;

use App\Entity\UserEntity\UserAccount;
use App\Entity\UserEntity\UserGroup;
// use App\Repository\UserEntity\UserGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Persistence\ObjectManager;
use ApiPlatform\Core\Annotation\ApiResource;

// https://api-platform.com/docs/distribution
// use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserEntityRepository")
 * @ApiResource
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
     * @ORM\Column(type="string", length=32)
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
     * @ORM\OneToOne(targetEntity="App\Entity\UserEntity\UserAccount", mappedBy="user", cascade={"persist", "remove"})
     */
    private $user_account;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserEntity\UserGroup", mappedBy="user")
     */
    private $user_groups;

    // private $objectManager;

    public function __construct(/*ObjectManager $objectManager*/)
    {
        // $this->objectManager = $objectManager;
        $this->user_groups   = new ArrayCollection();
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

    public function setLastname(string $lastname): self
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

    public function getUserAccount(): ?UserAccount
    {
        return $this->user_account;
    }

    public function setUserAccount(UserAccount $user_account): self
    {
        $this->user_account = $user_account;

        // set the owning side of the relation if necessary
        if ($this !== $user_account->getUser()) {
            $user_account->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|UserGroup[]
     */
    public function getUserGroups(): Collection
    {
        return $this->user_groups;
    }

    public function addUserGroup(UserGroup $userGroup): self
    {
        // if (!$this->user_groups->contains($userGroup)) {
            $this->user_groups[] = $userGroup;
            $userGroup->addUser($this);
        // }

        return $this;
    }

    // public function removeUserGroup(UserGroup $userGroup): self
    public function removeUserGroup($doctrine): self
    {
        $sql    = "delete from user_group_user_entity where user_entity_id = {$this->getID()}";
        $em     = $doctrine->getManager();
        // $em     = $this->objectManager;
        $stmt   = $em->getConnection()->prepare($sql);
        $stmt->execute();

        // // if ($this->user_groups->contains($userGroup)) {
        //     $this->user_groups->removeElement($userGroup);
        //     $userGroup->removeUser($this);
        // // }

        return $this;
    }

    public function saveUserGroups(array $requestAll, $doctrine): self
    {
        if ($this->getId()) {
            $this->removeUserGroup($doctrine);
        }
        // $currentGroups = $this->getUserGroups();
        $reqeustGroups = array_values($requestAll['user_entity']['user_groups'] ?? []);
        // foreach ($currentGroups as $currentGroup) {
        //     // if (!in_array($currentGroup->getId(), $reqeustGroups)) {
        //         $this->removeUserGroup($currentGroup);
        //     // }
        // }
        // $doctrine->getManager()->flush();
        foreach ($reqeustGroups as $groupId) {
            $this->addUserGroup($doctrine->getRepository(UserGroup::class)->find($groupId));
        }
        return $this;
    }
}
