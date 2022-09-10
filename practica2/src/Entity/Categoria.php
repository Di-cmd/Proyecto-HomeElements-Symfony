<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoriaRepository::class)
 */
class Categoria
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
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity=Producto::class, mappedBy="categoria")
     */
    private $producto;



    public function __construct()
    {
        $this->productos = new ArrayCollection();
        $this->producto = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

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
            $producto->setCategoria($this);
        }

        return $this;
    }

    public function removeProducto(Producto $producto): self
    {
        if ($this->producto->removeElement($producto)) {
            // set the owning side to null (unless already changed)
            if ($producto->getCategoria() === $this) {
                $producto->setCategoria(null);
            }
        }

        return $this;
    }

}
