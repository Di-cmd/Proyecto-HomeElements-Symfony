<?php

namespace App\Controller;

use App\Entity\Cliente;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClienteController extends AbstractController
{
    /**
     * @Route("/cliente", name="app_cliente")
     */
    public function index(): Response
    {
        return $this->render('cliente/cliente.html.twig', [
            'controller_name' => 'ClienteController',
        ]);
    }




    
//------------------------------------------------------------------------------------------------------
  //Este es el backend
//------------------------------------------------------------------------------------------------------  


    // Consultar Clientes: 
   /**
     * @Route("/clienteJSON", name="clienteJSON")
     */
    public function getClientes(){
        $clientesAlmacenados = $this->getDoctrine()->getRepository(Cliente::class)->findAll();
        // dd($productosAlmacenados);
        $array=[];
        for($i=0; $i<count($clientesAlmacenados); $i++){
           //var_dump($productosAlmacenados[$i]->getNombre() ;
           $array[$i]=[
                'id'=>$clientesAlmacenados[$i]->getId(),
                'nombre'=>$clientesAlmacenados[$i]->getNombre(),
                'correo'=>$clientesAlmacenados[$i]->getCorreo(),
                'departamento'=>$clientesAlmacenados[$i]->getDepartamento(),
                'municipio'=>$clientesAlmacenados[$i]->getMunicipio(),
           ];
        }
        return new JsonResponse(['clientes' => $array]);
    }



    // Crear Clientes: 
    /**
     * @Route("/crearCliente", name="crearCliente")
     */
    public function crearCliente(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $cliente = new Cliente();
        $cliente->setNombre($data['nombre']);
        $cliente->setCorreo($data['correo']);
        $cliente->setDepartamento($data['departamento']);
        $cliente->setMunicipio($data['municipio']);
        $entityManager->persist($cliente);
        $entityManager->flush();
        return new JsonResponse(['mensaje' => "Se creo el cliente con exito"]);
    }
    


    /**
     * @Route("/deleteCliente/{id}", name="deleteCliente")
     */

    public function deleteCliente(Cliente $cliente, ManagerRegistry $doctrine)
    {
       
        $entityManager = $doctrine->getManager();
        $entityManager->remove($cliente);
        $entityManager->flush();
        return new JsonResponse(['mensaje' =>"Se ha eliminado exitosamente"]);
    }





    
    /**
     * @Route("/editarCliente", name="editarCliente")
     */

    public function editarCliente(Request $request): Response
    {
        $data=json_decode($request->getContent(), true);
        $clienteEditar = $this->getDoctrine()->getRepository(Cliente::class)->find($data['id']);
        $entityManager = $this->getDoctrine()->getManager();
        $clienteEditar->setNombre($data['nombre']);
        $clienteEditar->setCorreo($data['correo']);
        $clienteEditar->setDepartamento($data['departamento']);
        $clienteEditar->setMunicipio($data['municipio']);
        $entityManager->persist($clienteEditar);
        $entityManager->flush();
        return new JsonResponse(['mensaje' => "Se Edito el cliente con exito"]);
    }




}
