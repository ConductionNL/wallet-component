<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ContractRepository;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An entity representing an node.
 *
 * entity that holds the contract object for the wallet component
 *
 * @author Robert Zondervan <Robert@conduction.nl>
 *
 * @category entity
 *
 * @license EUPL <https://github.com/ConductionNL/betaalservice/blob/master/LICENSE.md>
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true},
 *     itemOperations={
 *          "get",
 *          "get_change_logs"={
 *              "path"="/contracts/{id}/change_log",
 *              "method"="get",
 *              "swagger_context" = {
 *                  "summary"="Changelogs",
 *                  "description"="Gets al the change logs for this resource"
 *              }
 *          },
 *          "get_audit_trail"={
 *              "path"="/contracts/{id}/audit_trail",
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
 * @ORM\Entity(repositoryClass=ContractRepository::class)
 * @Gedmo\Loggable(logEntryClass="Conduction\CommonGroundBundle\Entity\ChangeLog")
 *
 * @ApiFilter(OrderFilter::class)
 * @ApiFilter(DateFilter::class, strategy=DateFilter::EXCLUDE_NULL)
 * @ApiFilter(SearchFilter::class, properties={"user": "exact", "organization": "exact"})
 */
class Contract
{
    /**
     * @var UuidInterface The UUID identifier of this object
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     *
     * @Groups({"read"})
     * @Assert\Uuid
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private UuidInterface $id;

    /**
     * @var string Name of this contract
     *
     * @example internship contract
     *
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @var string The description of the contract
     *
     * @example this is the best contract ever
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @var bool Denotes if the contract is signed
     *
     * @Groups({"read","write"})
     * @Assert\NotNull()
     * @ORM\Column(type="boolean")
     */
    private bool $signed;

    /**
     * @var DateTime The expiration date of the contract. Has to be set if noticePeriod is not.
     *
     * @example 2020-11-14
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $expiryDate;

    /**
     * @var DateInterval The notice period for the termination of the contract. Has to be set if expiryDate is not.
     *
     * @example P1M
     * @Groups({"read","write"})
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private ?DateInterval $noticePeriod;

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

    /**
     * @var Collection|Signee[] The signees of the contract
     *
     * @Groups({"read", "write"})
     *
     * @ORM\OneToMany(targetEntity=Signee::class, mappedBy="contract", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $signees;

    public function __construct()
    {
        $this->signees = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSigned(): ?bool
    {
        foreach($this->signees as $signee){
            if(!$signee->getSigned()){
                return false;
            }
        }
        $this->setSigned(true);
        return $this->signed;
    }

    public function setSigned(bool $signed): self
    {
        $this->signed = $signed;
        foreach($this->signees as $signee){
            $signee->setSigned($signed);
        }
        return $this;
    }

    public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(?\DateTimeInterface $expiryDate): self
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    public function getNoticePeriod(): ?\DateInterval
    {
        return $this->noticePeriod;
    }

    public function setNoticePeriod(?\DateInterval $noticePeriod): self
    {
        $this->noticePeriod = $noticePeriod;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(?\DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    public function getUsers(): ?array
    {
        return $this->users;
    }

    public function setUsers(array $users): self
    {
        $this->users = $users;

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

    /**
     * @return Collection|Signee[]
     */
    public function getSignees(): Collection
    {
        return $this->signees;
    }

    public function addSignee(Signee $signee): self
    {
        if (!$this->signees->contains($signee)) {
            $this->signees[] = $signee;
            $signee->setContract($this);
        }

        return $this;
    }

    public function removeSignee(Signee $signee): self
    {
        if ($this->signees->contains($signee)) {
            $this->signees->removeElement($signee);
            // set the owning side to null (unless already changed)
            if ($signee->getContract() === $this) {
                $signee->setContract(null);
            }
        }

        return $this;
    }
}
