<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Producto;

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
     * @ORM\ManyToOne(targetEntity=Producto::class, inversedBy="pedido")
     */
    private $producto;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $consecutivo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $totalPedido;


     /**
     * @ORM\Column(type="string", length=255)
     */
    private $cantidadProducto;




    public function __construct()
    {
        $this->producto = new ArrayCollection();
        $this->productos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigoPedido(): ?string
    {
        return $this->codigo;
    }

    public function setCodigoPedido(string $codigo = null): self
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

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;

        return $this;
    }

    public function getConsecutivo(): ?string
    {
        return $this->consecutivo;
    }

    public function setConsecutivo(string $consecutivo): self
    {
        $this->consecutivo = $consecutivo;

        return $this;
    }


    
    public function getTotalPedido(): ?string
    {
        return $this->totalPedido;
    }

    public function setTotalPedido(string $totalPedido): self
    {
        $this->totalPedido = $totalPedido;

        return $this;
    }




    public function getCantidadProducto(): ?string
    {
        return $this->cantidadProducto;
    }

    public function setCantidadProducto(string $cantidadProducto): self
    {
        $this->cantidadProducto = $cantidadProducto;
        return $this;
    }





}
