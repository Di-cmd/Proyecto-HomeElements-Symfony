<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\Cliente;
use App\Entity\Producto;
use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;


class ProductoController extends AbstractController
{

//---------------------------------------------------------------------------------------
//Rutas para el front
//-------------------------------------------------------------------------------------



    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('producto/producto.html.twig');
    }



    /**
     * @Route("/producto", name="producto")
     */
    public function productos(Request $request): Response
    {
        return $this->render('producto/producto.html.twig');
    }


   //--------------------------------------------------------------------------------------------------- 
    // Rutas de Backend: 
  //-----------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------
  //Para los Productos
//------------------------------------------------------------------------------------------------------  

   /**
     * @Route("/productoJSON", name="productoJSON")
     */
    public function getProductos(){
        $productosAlmacenados = $this->getDoctrine()->getRepository(Producto::class)->productosGeneral();
        $array=[];
        //dd($productosAlmacenados);
        for($i=0; $i<count($productosAlmacenados); $i++){
           //var_dump($productosAlmacenados[$i]->getNombre() ;
           $array[$i]=[
                'id'=>$productosAlmacenados[$i]['id'],
                'nombre'=>$productosAlmacenados[$i]['nombreP'],
                'codigo'=>$productosAlmacenados[$i]['codigo'],
                'categoria'=>$productosAlmacenados[$i]['nombre'],
                'cantidad'=>$productosAlmacenados[$i]['cantidad'],
                'precio'=>$productosAlmacenados[$i]['precio'],
                'estado'=>$productosAlmacenados[$i]['estado'],
           ];
        }
        
        return new JsonResponse(['productos' => $array]);
    }


    //Ruta para crear productos: 
    /**
     * @Route("/crearProducto", name="crearProducto")
     */
    public function crearProducto(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        // Tengo que buscar que si existe en la base de datos:
        $categoria = $this->getDoctrine()->getRepository(Categoria::class)->findOneBy(['nombre'=>$data['categoria']]);
       
        if(!$categoria) {
            $categoria = new Categoria();
            $categoria->setNombre($data['categoria']);
            $entityManager->persist($categoria);
            $entityManager->flush();
        }

        $producto = new Producto();
        $producto->setNombre($data['nombre']);
        $producto->setCodigo($data['codigo']);
        $producto->setCantidad($data['cantidad']);
        $producto->setPrecio($data['precio']);
        $producto->setEstado($data['estado']);
        $producto->setCategoria($categoria);
        $entityManager->persist($producto);
        $entityManager->flush();

        return new JsonResponse(['mensaje' => "Se creo el producto con exito"]);
    }
    

    //Ruta para eliminar productos: 

    /**
     * @Route("/deleteProducto/{id}", name="deleteProducto")
     */

    public function deleteProducto(Producto $producto, ManagerRegistry $doctrine)
    {
        $producto;
        $entityManager = $doctrine->getManager();
        $entityManager->remove($producto);
        $entityManager->flush();
        return new JsonResponse(['mensaje' =>"Se ha eliminado exitosamente"]);
    }



    /**
     * @Route("/editProducto", name="editarProducto")
     */

    public function editarProducto(Request $request): Response
    {
        $data=json_decode($request->getContent(), true);

        // Asi se recibe cuando llega por medio de un metodo get por medio de la url
        // $data=$request->getContent();

        $productoEditar = $this->getDoctrine()->getRepository(Producto::class)->find($data['id']);
        $entityManager = $this->getDoctrine()->getManager();
        $productoEditar->setNombre($data['nombre']);
        $productoEditar->setCodigo($data['codigo']);
        $productoEditar->setCantidad($data['cantidad']);
        $productoEditar->setPrecio($data['precio']);
        $productoEditar->setEstado($data['estado']);
        $entityManager->persist($productoEditar);
        $entityManager->flush();
        return new JsonResponse(['mensaje' => "Se Edito el producto con exito"]);
    }

}
