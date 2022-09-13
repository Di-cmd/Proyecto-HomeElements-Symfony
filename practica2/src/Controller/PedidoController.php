<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Pedido;
use App\Entity\Producto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PedidoController extends AbstractController
{
    /**
     * @Route("/pedido", name="app_pedido")
     */
    public function index(): Response
    {
        return $this->render('pedido/pedido.html.twig', [
            'controller_name' => 'PedidoController',
        ]);
    }



    //------------------------------------------------------------------------------------------------------
    //Este es el backend
    //------------------------------------------------------------------------------------------------------


    // Consultar Clientes:
    /**
     * @Route("/pedidoJSON", name="pedidoJSON")
     */
    public function getPedidos()
    {
        $pedidosAlmacenados = $this->getDoctrine()->getRepository(Pedido::class)->findAll();
        // dd($productosAlmacenados);
        $array = [];
        for ($i = 0; $i < count($pedidosAlmacenados); $i++) {
            //var_dump($productosAlmacenados[$i]->getNombre() ;
            $array[$i] = [
                'id' => $pedidosAlmacenados[$i]->getId(),
                'codigo' => $pedidosAlmacenados[$i]->getCodigoPedido(),
                'departamento' => $pedidosAlmacenados[$i]->getDepartamento(),
                'municipio' => $pedidosAlmacenados[$i]->getMunicipio(),
            ];
        }
        return new JsonResponse(['pedidos' => $array]);
    }



    // Crear Pedidos:
    /**
     * @Route("/crearPedido", name="crearPedido")
     */
    public function crearPedido(Request $request)
    {

        $incrementa=0;
        $conse=0;
        $pedidos = $this->getDoctrine()->getRepository(Pedido::class)->findAll();

       
        if(count($pedidos)>0){
            
            for($i=0;$i<count($pedidos);$i++)
            {
                $incrementa=$pedidos[$i];
            }
            dd(intval($incrementa['consecutivo']));

        }
        $data = json_decode($request->getContent(), true);
        for ($i = 0; $i < count($data['productoAgregado']); $i++) {
            $entityManager = $this->getDoctrine()->getManager();
            $pedido = new Pedido();
            $idProducto = $data['productoAgregado'][$i]['idProducto'];
            $producto = $this->getDoctrine()->getRepository(Producto::class)->find($idProducto);
            // se agrega un nuevo pedido: 
            $pedido->setCodigoPedido($data['codigo']);
            $pedido->setDepartamento($data['departamento']);
            $pedido->setMunicipio($data['municipio']);
            $pedido->setProducto($producto);
            $pedido->setConsecutivo($incrementa);
            $entityManager->persist($pedido);
            $entityManager->flush();
        }
        return new JsonResponse(['mensaje' => "Se creo el pedido con exito"]);
    }

    /**
     * @Route("/deletePedido/{id}", name="deletePedido")
     */
    public function deletePedido(Pedido $pedido, ManagerRegistry $doctrine): JsonResponse
    {
        $pedido;
        $entityManager = $doctrine->getManager();
        $entityManager->remove($pedido);
        $entityManager->flush();
        return new JsonResponse(['mensaje' => "Se ha eliminado exitosamente el pedido"]);
    }

    /**
     * @Route("/editarPedido", name="editarPedido")
     */
    public function editarPedido(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $pedidoEditar = $this->getDoctrine()->getRepository(Pedido::class)->find($data['id']);
        $entityManager = $this->getDoctrine()->getManager();
        $pedidoEditar->setgetCodigoPedido($data['codigo']);
        $pedidoEditar->setDepartamento($data['departamento']);
        $pedidoEditar->setMunicipio($data['municipio']);
        $entityManager->persist($pedidoEditar);
        $entityManager->flush();
        return new JsonResponse(['mensaje' => "Se Edito el pedido con exito"]);
    }

    /**
     * @Route("/pedidoCliente", name="pedidoCliente")
     */
    public function pedido(Cliente $cliente, Request $request)
    {
        return $this->render('pedido/pedido.html.twig');
    }
}
