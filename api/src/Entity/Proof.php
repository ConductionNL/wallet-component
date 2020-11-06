<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ProofRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proof is a way to prove that claims are correct.
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"=true}
 * )
 * @ORM\Entity(repositoryClass=ProofRepository::class)
 * @Gedmo\Loggable(logEntryClass="Conduction\CommonGroundBundle\Entity\ChangeLog")
 *
 * @ApiFilter(BooleanFilter::class)
 * @ApiFilter(OrderFilter::class)
 * @ApiFilter(DateFilter::class, strategy=DateFilter::EXCLUDE_NULL)
 * @ApiFilter(SearchFilter::class, properties={"claim.id": "exact"})
 */
class Proof
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
     * @var string The type of this Proof.
     *
     * @example RsaSignature2018
     *
     * @Gedmo\Versioned
     * @Assert\Length(
     *      max = 255
     * )
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string The purpose of this Proof.
     *
     * @example assertionMethod
     *
     * @Gedmo\Versioned
     * @Assert\Length(
     *      max = 255
     * )
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $proofPurpose;

    /**
     * @var string The identifier of the public key that can verify the signature.
     *
     * @example https://example.edu/issuers/keys/1
     *
     * @Gedmo\Versioned
     * @Assert\Url
     * @Assert\Length(
     *      max = 255
     * )
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $verificationMethod;

    /**
     * @var string The digital signature value.
     *
     * @example eyJhbGciOiJSUzI1NiIsImI2NCI6ZmFsc2UsImNyaXQiOlsiYjY0Il19..TCYt5XsITJX1CxPCT8yAV-TVkIEq_PbChOMqsLfRoPsnsgw5WEuts01mq-pQy7UJiN5mgRxD-WUcX16dUEMGlv50aqzpqh4Qktb3rk-BuQy72IFLOqV0G_zS245-kronKb78cPN25DGlcTwLtjPAYuNzVBAh4vGHSrQyHUdBBPM
     *
     * @Gedmo\Versioned
     * @Assert\Length(
     *      max = 255
     * )
     * @Gedmo\Versioned
     * @Groups({"read","write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $jws;

    /**
     * @var Datetime The moment this proof was created
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreated;

    /**
     * @var Datetime The moment this proof was last Modified
     *
     * @Groups({"read"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateModified;

    /**
     * @Groups({"read","write"})
     * @MaxDepth(1)
     * @ORM\ManyToOne(targetEntity=Claim::class, inversedBy="proofs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $claim;

    /**
     * @Groups({"read","write"})
     * @MaxDepth(1)
     * @ORM\ManyToOne(targetEntity=Application::class, inversedBy="proofs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $application;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getProofPurpose(): ?string
    {
        return $this->proofPurpose;
    }

    public function setProofPurpose(?string $proofPurpose): self
    {
        $this->proofPurpose = $proofPurpose;

        return $this;
    }

    public function getVerificationMethod(): ?string
    {
        return $this->verificationMethod;
    }

    public function setVerificationMethod(?string $verificationMethod): self
    {
        $this->verificationMethod = $verificationMethod;

        return $this;
    }

    public function getJws(): ?string
    {
        return $this->jws;
    }

    public function setJws(?string $jws): self
    {
        $this->jws = $jws;

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

    public function getClaim(): ?Claim
    {
        return $this->claim;
    }

    public function setClaim(?Claim $claim): self
    {
        $this->claim = $claim;

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
}
