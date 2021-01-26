<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\GroupRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An entity representing a group.
 *
 * entity that holds the groups linked to a application object
 *
 * @author Gino Kok <gino@conduction.nl>
 *
 * @category entity
 *
 * @license EUPL <https://github.com/ConductionNL/wallet-component/blob/master/LICENSE.md>
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true},
 *     itemOperations={
 *          "get",
 *          "put",
 *          "delete",
 *          "get_change_logs"={
 *              "path"="/groups/{id}/change_log",
 *              "method"="get",
 *              "swagger_context" = {
 *                  "summary"="Changelogs",
 *                  "description"="Gets al the change logs for this resource"
 *              }
 *          },
 *          "get_audit_trail"={
 *              "path"="/groups/{id}/audit_trail",
 *              "method"="get",
 *              "swagger_context" = {
 *                  "summary"="Audittrail",
 *                  "description"="Gets the audit trail for this resource"
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get",
 *          "post"
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @Gedmo\Loggable(logEntryClass="Conduction\CommonGroundBundle\Entity\ChangeLog")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="userGroup")
 *
 * @ApiFilter(OrderFilter::class)
 * @ApiFilter(DateFilter::class, strategy=DateFilter::EXCLUDE_NULL)
 * @ApiFilter(SearchFilter::class, properties={"application.id": "exact", "application": "exact", "memberships.userUrl": "exact"})
 */
class Group
{
    /**
     * @var UuidInterface The UUID identifier of this object
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     * @Groups({"read"})
     * @Assert\Uuid
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string Name of this group
     *
     * @example administrators
     *
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string description of this group
     *
     * @example group that holds all the administrators
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string The organization linked to this group (uri or string) that can be used to retrieve a organization object
     *
     * @example https://example.org/organizations/1
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $organization;

    /**
     * @Groups({"read", "write"})
     *
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="childGroups")
     */
    private $parentGroup;

    /**
     * @Groups({"read"})
     * @ORM\OneToMany(targetEntity=Group::class, mappedBy="parentGroup")
     */
    private $childGroups;

    /**
     * @Assert\NotNull()
     * @Groups({"read", "write"})
     * @MaxDepth(1)
     * @ORM\ManyToOne(targetEntity=Application::class, inversedBy="userGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $application;

    /**
     * @Groups({"read","write"})
     * @MaxDepth(1)
     * @ORM\OneToMany(targetEntity=Membership::class, mappedBy="userGroup", orphanRemoval=true)
     */
    private $memberships;

    /**
     * @var DateTime The moment this request was created by the submitter
     *
     * @example 20190101
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @var DateTime The moment this request was created by the submitter
     *
     * @example 20190101
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModified;

    public function __construct()
    {
        $this->childGroups = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getParentGroup(): ?self
    {
        return $this->parentGroup;
    }

    public function setParentGroup(?self $parentGroup): self
    {
        $this->parentGroup = $parentGroup;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildGroups(): Collection
    {
        return $this->childGroups;
    }

    public function addChildGroup(self $childGroup): self
    {
        if (!$this->childGroups->contains($childGroup)) {
            $this->childGroups[] = $childGroup;
            $childGroup->setParentGroup($this);
        }

        return $this;
    }

    public function removeChildGroup(self $childGroup): self
    {
        if ($this->childGroups->contains($childGroup)) {
            $this->childGroups->removeElement($childGroup);
            // set the owning side to null (unless already changed)
            if ($childGroup->getParentGroup() === $this) {
                $childGroup->setParentGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Membership[]
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(Membership $membership): self
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships[] = $membership;
            $membership->setUserGroup($this);
        }

        return $this;
    }

    public function removeMembership(Membership $membership): self
    {
        if ($this->memberships->contains($membership)) {
            $this->memberships->removeElement($membership);
            // set the owning side to null (unless already changed)
            if ($membership->getUserGroup() === $this) {
                $membership->setUserGroup(null);
            }
        }

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateModified(): ?DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }
}
