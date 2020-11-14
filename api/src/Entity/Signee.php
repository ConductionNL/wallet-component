<?php

namespace App\Entity;

use App\Repository\SigneeRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An entity representing a signee.
 *
 * entity that holds the signee object for contracts in the wallet component
 *
 * @author Robert Zondervan <Robert@conduction.nl>
 *
 * @category entity
 *
 * @license EUPL <https://github.com/ConductionNL/wallet-component/blob/master/LICENSE.md>
 * @ORM\Entity(repositoryClass=SigneeRepository::class)
 */
class Signee
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
    private UuidInterface $id;

    /**
     * @var string Name of this Signee
     *
     * @example Robert Zondervan
     *
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @var string E-mail address of this Signee
     *
     * @example Robert@conduction.nl
     *
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255)
     */
    private string $email;

    /**
     * @var string Phone number of this Signee
     *
     * @example +31 (0)20 1234567
     *
     * @Groups({"read","write"})
     * @Assert\NotNull
     * @ORM\Column(type="string", length=255)
     */
    private string $phone;

    /**
     * @var bool Denotes if the signee has signed the contract
     *
     * @Groups({"read","write"})
     * @Assert\NotNull()
     * @ORM\Column(type="boolean")
     */
    private bool $signed = false;

    /**
     * @ORM\ManyToOne(targetEntity=Contract::class, inversedBy="signees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contract;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getSigned(): ?bool
    {
        return $this->signed;
    }

    public function setSigned(bool $signed): self
    {
        $this->signed = $signed;

        return $this;
    }
}
