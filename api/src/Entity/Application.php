<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ApplicationRepository;
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
 * An entity representing an node.
 *
 * entity that holds the application object for wallet component
 *
 * @author Gino Kok <gino@conduction.nl>
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
 *          "put",
 *          "delete",
 *          "get_change_logs"={
 *              "path"="/applications/{id}/change_log",
 *              "method"="get",
 *              "swagger_context" = {
 *                  "summary"="Changelogs",
 *                  "description"="Gets al the change logs for this resource"
 *              }
 *          },
 *          "get_audit_trail"={
 *              "path"="/applications/{id}/audit_trail",
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
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 * @Gedmo\Loggable(logEntryClass="Conduction\CommonGroundBundle\Entity\ChangeLog")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="application_secret", columns={"secret"})})
 *
 * @ApiFilter(OrderFilter::class)
 * @ApiFilter(DateFilter::class, strategy=DateFilter::EXCLUDE_NULL)
 * @ApiFilter(SearchFilter::class, properties={"organization": "exact", "secret": "exact", "name": "exact", "id": "exact"})
 */
class Application
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
    private $id;

    /**
     * @var string Name of this application
     *
     * @example stage platform
     *
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string authorization url of the application
     *
     * @example stage platform
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authorizationUrl;

    /**
     * @var string webhook url of the application
     *
     * @example stage platform
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $webhookUrl;

    /**
     * @var string single sign on url of the application
     *
     * @example stage platform
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $singleSignOnUrl;

    /**
     * @var string Random generated secret for the application
     *
     * @Gedmo\Versioned
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     * @Groups({"read"})
     * @Assert\Length(
     *     max=255
     * )
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private $secret;

    /**
     * @var string Random generated secret for the application
     *
     * @Gedmo\Versioned
     *
     * @example e2984465-190a-4562-829e-a8cca81aa35d
     * @Groups({"read"})
     * @Assert\Length(
     *     max=255
     * )
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private $testSecret;

    /**
     * @var string description of this application
     *
     * @example stage platform description
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string The organization this application belongs to
     *
     * @example https://example.org/organizations/1
     *
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @Assert\Url
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $organization;

    /**
     * @var string The wrc application linked to this application
     *
     * @example https://example.org/organizations/1
     *
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @Assert\Url
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $contact;

    /**
     * @var string The endpoint of this application.
     *
     * @example https://dev.zuid-drecht.nl/notification
     *
     * @Assert\Url
     * @Assert\Length(
     *     max = 255
     * )
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notificationEndpoint;

    /**
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="json")
     */
    private $scopes = [];

    /**
     * @var string The gdprContact of this application
     *
     * @example https://example.org/organizations/1
     *
     * @Groups({"read","write"})
     * @Assert\Url
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gdprContact;

    /**
     * @var string The technicalContact of this application
     *
     * @example https://example.org/organizations/1
     *
     * @Groups({"read","write"})
     * @Assert\Url
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $technicalContact;

    /**
     * @var string The privacyContact of this application
     *
     * @example https://example.org/organizations/1
     *
     * @Groups({"read","write"})
     * @Assert\Url
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $privacyContact;

    /**
     * @var string The billingContact of this application
     *
     * @example https://example.org/organizations/1
     *
     * @Groups({"read","write"})
     * @Assert\Url
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingContact;

    /**
     * @var string The mailgun api key of this application
     *
     * @example 9dja5d6a6dasda-dsadas6azd-dz5dzadzasdd5e45ad5a3g223
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mailgunApiKey;

    /**
     * @var string The mailgun domain of this application
     *
     * @example mail.zuid-drecht.nl
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mailgunDomain;

    /**
     * @var string The messageBird api key of this application
     *
     * @example 9dja5d6a6dasda-dsadas6azd-dz5dzadzasdd5e45ad5a3g223
     *
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $messageBirdApiKey;

    /**
     * @ORM\OneToMany(targetEntity=Authorization::class, mappedBy="application", orphanRemoval=true)
     */
    private $authorizations;

    /**
     * @Groups({"read","write"})
     * @MaxDepth(1)
     * @ORM\OneToMany(targetEntity=Proof::class, mappedBy="application", orphanRemoval=true)
     */
    private $proofs;

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
     *  @ORM\PrePersist
     *  @ORM\PreUpdate
     *
     *  */
    public function prePersist()
    {
        if (!$this->getSecret()) {
            $secret = Uuid::uuid4();
            $this->setSecret($secret);
        }

        if (!$this->getTestSecret()) {
            $secret = 'test_'.Uuid::uuid4()->toString();
            $this->setTestSecret($secret);
        }
    }

    public function __construct()
    {
        $this->authorizations = new ArrayCollection();
        $this->proofs = new ArrayCollection();
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

    public function getAuthorizationUrl(): ?string
    {
        return $this->authorizationUrl;
    }

    public function setAuthorizationUrl(string $authorizationUrl): self
    {
        $this->authorizationUrl = $authorizationUrl;

        return $this;
    }

    public function getSingleSignOnUrl(): ?string
    {
        return $this->singleSignOnUrl;
    }

    public function setSingleSignOnUrl(string $singleSignOnUrl): self
    {
        $this->singleSignOnUrl = $singleSignOnUrl;

        return $this;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(string $webhookUrl): self
    {
        $this->webhookUrl = $webhookUrl;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getTestSecret(): ?string
    {
        return $this->testSecret;
    }

    public function setTestSecret(string $testSecret): self
    {
        $this->testSecret = $testSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
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
     * @return string
     */
    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotificationEndpoint(): ?string
    {
        return $this->notificationEndpoint;
    }

    public function setNotificationEndpoint(string $notificationEndpoint): self
    {
        $this->notificationEndpoint = $notificationEndpoint;

        return $this;
    }

    /**
     * @return string
     */
    public function getGdprContact(): ?string
    {
        return $this->gdprContact;
    }

    public function setGdprContact(string $gdprContact): self
    {
        $this->gdprContact = $gdprContact;

        return $this;
    }

    /**
     * @return string
     */
    public function getTechnicalContact(): ?string
    {
        return $this->technicalContact;
    }

    public function setTechnicalContact(string $technicalContact): self
    {
        $this->technicalContact = $technicalContact;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrivacyContact(): ?string
    {
        return $this->privacyContact;
    }

    public function setPrivacyContact(string $privacyContact): self
    {
        $this->privacyContact = $privacyContact;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingContact(): ?string
    {
        return $this->billingContact;
    }

    public function setBillingContact(string $billingContact): self
    {
        $this->billingContact = $billingContact;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailgunApiKey(): ?string
    {
        return $this->mailgunApiKey;
    }

    public function setMailgunApiKey(?string $mailgunApiKey): self
    {
        $this->mailgunApiKey = $mailgunApiKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailgunDomain(): ?string
    {
        return $this->mailgunDomain;
    }

    public function setMailgunDomain(?string $mailgunDomain): self
    {
        $this->mailgunDomain = $mailgunDomain;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessageBirdApiKey(): ?string
    {
        return $this->messageBirdApiKey;
    }

    public function setMessageBirdApiKey(?string $messageBirdApiKey): self
    {
        $this->messageBirdApiKey = $messageBirdApiKey;

        return $this;
    }

    /**
     * @return Collection|Authorization[]
     */
    public function getAuthorizations(): Collection
    {
        return $this->authorizations;
    }

    public function addAuthorization(Authorization $authorization): self
    {
        if (!$this->authorizations->contains($authorization)) {
            $this->authorizations[] = $authorization;
            $authorization->setApplication($this);
        }

        return $this;
    }

    public function removeAuthorization(Authorization $authorization): self
    {
        if ($this->authorizations->contains($authorization)) {
            $this->authorizations->removeElement($authorization);
            // set the owning side to null (unless already changed)
            if ($authorization->getApplication() === $this) {
                $authorization->setApplication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Proof[]
     */
    public function getProofs(): Collection
    {
        return $this->proofs;
    }

    public function addProof(Proof $proof): self
    {
        if (!$this->proofs->contains($proof)) {
            $this->proofs[] = $proof;
            $proof->setApplication($this);
        }

        return $this;
    }

    public function removeProof(Proof $proof): self
    {
        if ($this->proofs->contains($proof)) {
            $this->proofs->removeElement($proof);
            // set the owning side to null (unless already changed)
            if ($proof->getApplication() === $this) {
                $proof->setApplication(null);
            }
        }

        return $this;
    }

    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): self
    {
        $this->scopes = $scopes;

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
