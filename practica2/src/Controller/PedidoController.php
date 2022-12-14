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
     * @Route("/pedidoJSON/{id}", name="pedidoJSON")
     */
    public function getPedidos($id, ManagerRegistry $doctrine)
    {
        //Lo convierto a numerico:
        $id = intval($id);
        $pedidosAlmacenados = $this->getDoctrine()->getRepository(Pedido::class)->PedidoConsecutivo($id);
        //dd($pedidosAlmacenados);
        $array = [];
        for ($i = 0; $i < count($pedidosAlmacenados); $i++) {
            //var_dump($productosAlmacenados[$i]->getNombre() ;
            $array[$i] = [
                'id' => $pedidosAlmacenados[$i]['id'],
                'codigo' => $pedidosAlmacenados[$i]['codigo'],
                'departamento' => $pedidosAlmacenados[$i]['departamento'],
                'municipio' => $pedidosAlmacenados[$i]['municipio'],
                'totalPedido' => $pedidosAlmacenados[$i]['totalPedido'],
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

        $incrementa = 0;
        $pedidos = $this->getDoctrine()->getRepository(Pedido::class)->findAll();
        // esto permite colocar un consecutivo diferente a los pedidos
        if (count($pedidos) > 0) {
            for ($i = 0; $i < count($pedidos); $i++) {
                $incrementa = intval($pedidos[$i]->getConsecutivo());
            }
            $incrementa = $incrementa + 1;
        }

        $data = json_decode($request->getContent(), true);
        $idCliente = $data['cliente'];
        $cliente = $this->getDoctrine()->getRepository(Cliente::class)->find($idCliente);

        for ($i = 0; $i < count($data['productoAgregado']); $i++) {
            $entityManager = $this->getDoctrine()->getManager();
            $idProducto = $data['productoAgregado'][$i]['idProducto'];
            $producto = $this->getDoctrine()->getRepository(Producto::class)->find($idProducto);
            
            // MANTENER ACTUALIZADO EL INVENTARIO DE PRODUCTOS: 
            $cantidadExistenteProducto = intval($producto->getCantidad());
            $cantidadProductoPedido = intval($data['productoAgregado'][$i]['cantidadProducto']);
            $cantidadExistenteActualizada = $cantidadExistenteProducto - $cantidadProductoPedido;
            $producto->setCantidad($cantidadExistenteActualizada);
            $entityManager->persist($producto);
            $entityManager->flush();
            // Esto me permite Mantener actualizo el inventario de productos con las cantidades reales
    
            // AGREGAR UN NUEVO PEDIDO- LO CREA SEGUN LA CANTIDAD DE PRODUCTOS QUE HAYA. 
            $pedido = new Pedido();
            $pedido->setCodigoPedido($data['codigo']);
            $pedido->setDepartamento($data['departamento']);
            $pedido->setMunicipio($data['municipio']);
            $pedido->setProducto($producto);
            $pedido->setConsecutivo($incrementa);
            $pedido->setCliente($cliente);
            $pedido->setTotalPedido($data['totalPedido']);
            $pedido->setCantidadProducto($data['productoAgregado'][$i]['cantidadProducto']);
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
        $entityManager = $this->getDoctrine()->getManager();

        // dd($data['codigo'],$data['departamento'],$data['municipio']);
        $pedidoEditar = $this->getDoctrine()->getRepository(Pedido::class)->findBy(['consecutivo'=>$data['id']]);

        for($i = 0;$i < count($pedidoEditar); $i++){
            $pedidoEditar[$i]->setCodigoPedido($data['codigo']);
            $pedidoEditar[$i]->setDepartamento($data['departamento']);
            $pedidoEditar[$i]->setMunicipio($data['municipio']);
            $entityManager->persist($pedidoEditar[$i]);
            $entityManager->flush();
        }
       
        return new JsonResponse(['mensaje' => "Se Edito el pedido con exito"]);
    }


    /**
     * @Route("/buscarProductos/{id}", name="buscarProductos")
     */
    public function buscarProductos($id,Request $request): Response
    {
        $productoPorPedido = $this->getDoctrine()->getRepository(Pedido::class)->buscarProductoPorPedido($id);
        return new JsonResponse(['productosAsociados' => $productoPorPedido]);
    }




   /**
     * @Route("/enviarCorreo", name="enviarCorreo")
     */
    public function enviarCorreo(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        // dd("esto es lo que llega al controlador",$data);

        $email='ingeniero.desarrollo8@comersantander.com';
        $asunto='Prueba envio';
        $msg='Hola, se envia un mensaje desde php';
        $headers =  'MIME-Version: 1.0' . "\r\n"; 
        $headers .= 'From: Your name <info@address.com>' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 

        ini_set("SMTP","localhost");
        ini_set("smtp_port","25");
        ini_set("sendmail_from","00000@gmail.com");
        ini_set("sendmail_path", "C:\wamp\bin\sendmail.exe -t");


        
        mail($email,$asunto,$msg,$headers,"From: me@you.com");

        return new JsonResponse(['mensaje' => 'Se envio el correo con exito']);
    }














    // /**
    //  * @Route("/pedidoCliente/{id}", name="pedidoCliente")
    //  */
    // public function pedidoCliente(Cliente $cliente, Request $request): Response
    // {
    //     //dd($cliente);
    //     return $this->render('pedido/pedido.html.twig', [
    //         'controller_name' => 'PedidoController',
    //     ]);
    // }







}
