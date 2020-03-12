<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserOrderRepository")
 * @ORM\Table(name="user_orders")
 */
class UserOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $items;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pagseguro_code;

    /**
     * @ORM\Column(type="integer")
     */
    private $pagseguro_status;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable()
     */
    private $updated_at;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $reference;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="orderCollection")
	 */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItems(): ?string
    {
        return $this->items;
    }

    public function setItems(string $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getPagseguroCode(): ?string
    {
        return $this->pagseguro_code;
    }

    public function setPagseguroCode(string $pagseguro_code): self
    {
        $this->pagseguro_code = $pagseguro_code;

        return $this;
    }

    public function getPagseguroStatus(): ?int
    {
        return $this->pagseguro_status;
    }

    public function setPagseguroStatus(int $pagseguro_status): self
    {
        $this->pagseguro_status = $pagseguro_status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

	public function getReference(): ?string
	{
		return $this->reference;
	}

	public function setReference(string $reference): self
	{
		$this->reference = $reference;

		return $this;
	}

    public function setUser(User $user)
    {
    	$this->user = $user;
    }

}
