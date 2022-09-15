<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ProductoRepository::class)
 */
class Producto
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
    private $nombreP;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cantidad;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $precio;


    /**
     * @ORM\ManyToOne(targetEntity=Categoria::class, inversedBy="producto",cascade={"persist"}))
     */
    private $categoria;

    /**
     * @ORM\OneToMany(targetEntity=Pedido::class, mappedBy="producto")
     */
    private $pedido;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $estado;

    
    public function __construct()
    {
        $this->pedido = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombreP;
    }

    public function setNombre(string $nombreP): self
    {
        $this->nombreP = $nombreP;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getCantidad(): ?string
    {
        return $this->cantidad;
    }

    public function setCantidad(string $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }



    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): self
    {
        $this->precio = $precio;

        return $this;
    }



    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }


    public function getEstado(): ?string
    {
        return $this->estado;
    }


    public function setEstado(string $estado): self
    {
        $this->estado = $estado;
        return $this;
    }








    /**
     * @return Collection<int, Pedido>
     */
    public function getPedido(): Collection
    {
        return $this->pedido;
    }

    public function addPedido(Pedido $pedido): self
    {
        if (!$this->pedido->contains($pedido)) {
            $this->pedido[] = $pedido;
            $pedido->setProducto($this);
        }

        return $this;
    }

    public function removePedido(Pedido $pedido): self
    {
        if ($this->pedido->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getProducto() === $this) {
                $pedido->setProducto(null);
            }
        }

        return $this;
    }
}
