<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PedidoRepository::class)
 */
class Pedido
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $departamento;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $municipio;

    /**
     * @ORM\ManyToOne(targetEntity=Cliente::class, inversedBy="pedido")
     */
    private $cliente;

    /**
     * @ORM\OneToMany(targetEntity=Producto::class, mappedBy="pedido")
     */
    private $producto;

    public function __construct()
    {
        $this->producto = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigoPedido(): ?string
    {
        return $this->codigo;
    }

    public function setgetCodigoPedido(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getDepartamento(): ?string
    {
        return $this->departamento;
    }

    public function setDepartamento(string $departamento): self
    {
        $this->departamento = $departamento;

        return $this;
    }


    public function getMunicipio(): ?string
    {
        return $this->municipio;
    }

    public function setMunicipio(string $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }


    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * @return Collection<int, Producto>
     */
    public function getProducto(): Collection
    {
        return $this->producto;
    }

    public function addProducto(Producto $producto): self
    {
        if (!$this->producto->contains($producto)) {
            $this->producto[] = $producto;
            $producto->setPedido($this);
        }

        return $this;
    }

    public function removeProducto(Producto $producto): self
    {
        if ($this->producto->removeElement($producto)) {
            // set the owning side to null (unless already changed)
            if ($producto->getPedido() === $this) {
                $producto->setPedido(null);
            }
        }

        return $this;
    }
}
