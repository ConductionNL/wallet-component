<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\AuthorizationRepository;
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
 * Authorization gives an application access to data in claims.
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass=AuthorizationRepository::class)
 * @Gedmo\Loggable(logEntryClass="Conduction\CommonGroundBundle\Entity\ChangeLog")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="authorization_table")
 *
 * @ApiFilter(BooleanFilter::class)
 * @ApiFilter(OrderFilter::class)
 * @ApiFilter(DateFilter::class, strategy=DateFilter::EXCLUDE_NULL)
 * @ApiFilter(SearchFilter::class, properties={"userUrl": "exact", "application": "partial", "code": "exact", "id": "exact"})
 */
class Authorization
{
    /**
     * @var UuidInterface The UUID identifier of this resource
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     *
     * @Assert\Uuid
     * @Groups({"read"})
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string The person who makes his data available.
     *
     * @example https://dev.zuid-drecht.nl/api/v1/cc/people/{{uuid}]
     *
     * @Assert\Url
     * @Assert\Length(
     *     max = 255
     * )
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userUrl;

    /**
     * @var string Indicator whether user is new or not
     * @Groups({"read", "write"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $newUser = true;

    /**
     * @var array scopes this authorization has access to
     *
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="json")
     */
    private $scopes = [];

    /**
     * @var string Random generated code for authorization
     *
     * @Gedmo\Versioned
     *
     * @example 4Ad9sdDJA4123AS4Ad9sdDJA4123AS
     * @Groups({"read","write"})
     * @Assert\Length(
     *     max=255
     * )
     * @ORM\Column(type="string", length=30, nullable=true, unique=true)
     */
    private $code;

    /**
     * @var string The goal of this Authorization (what are the data used for).
     *
     * @example Get email adres for sending news every week
     *
     * @Gedmo\Versioned
     * @Assert\NotNull
     * @Assert\Length(
     *      max = 255
     * )
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255)
     */
    private $goal;

    /**
     * @var int The weight of the authorization in points
     *
     * @example 4
     *
     * @Gedmo\Versioned
     * @Groups({"read"})
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $points = 0;

    /**
     * @var Application The node where this checkin takes place
     *
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity=Application::class, inversedBy="authorizations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $application;

    /**
     * @var Datetime The date access was obtained to the data of this authorization
     *
     * @Groups({"read", "write"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startingDate;

    /**
     * @var Datetime The moment this authorization was created
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @var Datetime The moment this authorization was last Modified
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModified;

    /**
     * @Groups({"write"})
     * @MaxDepth(1)
     * @ORM\ManyToMany(targetEntity=Claim::class, mappedBy="authorizations")
     */
    private $claims;

    /**
     * @Groups({"write"})
     * @MaxDepth(1)
     * @ORM\OneToOne(targetEntity=PurposeLimitation::class, mappedBy="authorization", orphanRemoval=true, cascade={"persist"})
     */
    private $purposeLimitation;

    /**
     * @Groups({"write"})
     * @MaxDepth(1)
     * @ORM\OneToMany(targetEntity=Dossier::class, mappedBy="authorization")
     */
    private $dossiers;

    /**
     * @Groups({"write"})
     * @MaxDepth(1)
     * @ORM\OneToMany(targetEntity=ScopeRequest::class, mappedBy="authorization")
     */
    private $scopeRequests;

    /**
     * @Groups({"write"})
     * @MaxDepth(1)
     * @ORM\OneToMany(targetEntity=AuthorizationLog::class, mappedBy="authorization", orphanRemoval=true)
     */
    private $authorizationLogs;

    /**
     *  @ORM\PrePersist
     *  @ORM\PreUpdate
     *
     *  */
    public function prePersist()
    {
        if (!$this->getCode()) {
            $validChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

            $code = substr(str_shuffle(str_repeat($validChars, ceil(30 / strlen($validChars)))), 1, 30);
            $this->setCode($code);
        }

        $scopes = $this->getScopes();
        if (count($scopes) > $this->getPoints()) {
            $this->setPoints(count($scopes));
        }
    }

    public function __construct()
    {
        $this->claims = new ArrayCollection();
        $this->dossiers = new ArrayCollection();
        $this->scopeRequests = new ArrayCollection();
        $this->authorizationLogs = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUserUrl(): ?string
    {
        return $this->userUrl;
    }

    public function setUserUrl(?string $userUrl): self
    {
        $this->userUrl = $userUrl;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    public function setScopes(?array $scopes): self
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(string $goal): self
    {
        $this->goal = $goal;

        return $this;
    }

    public function getNewUser(): ?bool
    {
        return $this->newUser;
    }

    public function setNewUser(?bool $newUser): self
    {
        $this->newUser = $newUser;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getStartingDate(): ?\DateTimeInterface
    {
        return $this->startingDate;
    }

    public function setStartingDate(\DateTimeInterface $startingDate): self
    {
        $this->startingDate = $startingDate;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(\DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * @return Collection|Claim[]
     */
    public function getClaims(): Collection
    {
        return $this->claims;
    }

    public function addClaim(Claim $claim): self
    {
        if (!$this->claims->contains($claim)) {
            $this->claims[] = $claim;
            $claim->addAuthorization($this);
        }

        return $this;
    }

    public function removeClaim(Claim $claim): self
    {
        if ($this->claims->contains($claim)) {
            $this->claims->removeElement($claim);
            $claim->removeAuthorization($this);
        }

        return $this;
    }

    public function getPurposeLimitation(): ?PurposeLimitation
    {
        return $this->purposeLimitation;
    }

    public function setPurposeLimitation(PurposeLimitation $purposeLimitation): self
    {
        $this->purposeLimitation = $purposeLimitation;

        // set the owning side of the relation if necessary
        if ($purposeLimitation->getAuthorization() !== $this) {
            $purposeLimitation->setAuthorization($this);
        }

        return $this;
    }

    /**
     * @return Collection|Dossier[]
     */
    public function getDossiers(): Collection
    {
        return $this->dossiers;
    }

    public function addDossier(Dossier $dossier): self
    {
        if (!$this->dossiers->contains($dossier)) {
            $this->dossiers[] = $dossier;
            $dossier->setAuthorization($this);
        }

        return $this;
    }

    public function removeDossier(Dossier $dossier): self
    {
        if ($this->dossiers->contains($dossier)) {
            $this->dossiers->removeElement($dossier);
            $dossier->removeAuthorization($this);
        }

        return $this;
    }

    /**
     * @return Collection|AuthorizationLog[]
     */
    public function getAuthorizationLogs(): Collection
    {
        return $this->authorizationLogs;
    }

    public function addAuthorizationLog(AuthorizationLog $authorizationLog): self
    {
        if (!$this->authorizationLogs->contains($authorizationLog)) {
            $this->authorizationLogs[] = $authorizationLog;
            $authorizationLog->setAuthorization($this);
        }

        return $this;
    }

    public function removeAuthorizationLog(AuthorizationLog $authorizationLog): self
    {
        if ($this->authorizationLogs->contains($authorizationLog)) {
            $this->authorizationLogs->removeElement($authorizationLog);
            // set the owning side to null (unless already changed)
            if ($authorizationLog->getAuthorization() === $this) {
                $authorizationLog->setAuthorization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ScopeRequest[]
     */
    public function getScopeRequests(): Collection
    {
        return $this->scopeRequests;
    }

    public function addScopeRequest(ScopeRequest $scopeRequest): self
    {
        if (!$this->scopeRequests->contains($scopeRequest)) {
            $this->scopeRequests[] = $scopeRequest;
            $scopeRequest->setAuthorization($this);
        }

        return $this;
    }

    public function removeScopeRequest(ScopeRequest $scopeRequest): self
    {
        if ($this->scopeRequests->contains($scopeRequest)) {
            $this->scopeRequests->removeElement($scopeRequest);
            // set the owning side to null (unless already changed)
            if ($scopeRequest->getAuthorization() === $this) {
                $scopeRequest->setAuthorization(null);
            }
        }

        return $this;
    }
}
