let formProductos = {
  id: 0,
  nombre: "",
  codigo: 0,
  cantidad: 0,
  categoria:"",
}

new Vue({
  el: "#app",
  delimiters: ["${", "}"],
  data: {
    formProductos,
    mensaje: "holaaaa desde vue js para la vista de productos  ",
    productos: [],
    mensajeGuardado: "",
    estadoEditar: 0,
    estadoId: 0,
    producto: 0,
    
  },
  mounted() {
    this.getProductos();
  },
  methods: {
    async getProductos() {
        let productos= await axios.get('productoJSON');
        this.productos = productos.data.productos
    },


    async crearProducto() {
      let formulario = await fetch(crearProducto, {
        method: "POST",
        body: JSON.stringify(this.formProductos),
      });
      formulario = await formulario.json();
      this.mensajeGuardado = formulario["mensaje"];
      this.getProductos();
      this.formProductos = JSON.parse(JSON.stringify(formProductos))
    },

    //Para el metodo eliminar Tengo que mandar el parametro, por medio de la ruta
    async deleteProduct(id) {
      let eliminar = await axios("http://127.0.0.1:8080/deleteProducto/" + id, {
        method: "POST",
      });
      eliminar = await eliminar;
      eliminar = eliminar.data;
      this.mensajeGuardado = eliminar["mensaje"];
      this.getProductos();
    },

    async editProduct(estado, idProducto, producto) {

      this.estadoEditar = estado;
      this.estadoId = idProducto;
      this.producto = producto;

      if (this.producto != 0) {
        let editar = await axios.put("http://127.0.0.1:8080/editProducto", this.producto);
        this.mensajeGuardado = editar.data.mensaje;
        this.getProductos();
      } 
      else {
        console.log("este es el producto", this.producto);
        this.getProductos();
      }

    },
  },
  computed: {},
});
