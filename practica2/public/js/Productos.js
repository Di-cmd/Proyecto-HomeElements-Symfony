let formProductos = {
  id: 0,
  nombre: "",
  codigo: "",
  cantidad: 0,
  precio: 0,
  categoria: "",
  estado: "",
};

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
    nuevoP: 0,
    estadoP: true,
    rojitoClientes: false,
  },
  mounted() {
    this.getProductos();
  },
  methods: {
    async nuevoProducto(id) {
      this.nuevoP = id;
    },

    async getProductos() {
      let productos = await axios.get("productoJSON");
      this.productos = productos.data.productos;
    },

    async crearProducto() {
      if (this.formProductos.nombre != "" && this.formProductos.codigo!= 0
       && this.formProductos.cantidad != 0 && this.formProductos.precio!= 0 
       && this.formProductos.categoria!="" && this.formProductos.estado !=""
        ) {

        let formulario = await fetch(crearProducto, {
          method: "POST",
          body: JSON.stringify(this.formProductos),
        });
        formulario = await formulario.json();
        this.mensajeGuardado = formulario["mensaje"];
        this.getProductos();
        this.formProductos = JSON.parse(JSON.stringify(formProductos));
        this.nuevoP = 0;
        // limpiar el formulario:
        this.formProductos.nombre = "";
        this.formProductos.codigo = 0;
        this.formProductos.cantidad = 0;
        this.formProductos.precio = 0;
        this.formProductos.categoria = "";
      } else {
        alert("Por favor debe diligenciar todos los campos");
      }
    },

    //Para el metodo eliminar Tengo que mandar el parametro, por medio de la ruta
    async deleteProduct(id) {
      this.estadoP = false;

      let eliminar = await axios("http://127.0.0.1:8080/deleteProducto/" + id, {
        method: "POST",
      });
      eliminar = await eliminar;
      eliminar = eliminar.data;
      this.mensajeGuardado = eliminar["mensaje"];
      this.getProductos();
    },

    async editProduct(estado, idProducto, producto) {
      console.log(producto);
      this.estadoEditar = estado;
      this.estadoId = idProducto;
      this.producto = producto;

      if (this.producto != 0) {
        let editar = await axios.put(
          "http://127.0.0.1:8080/editProducto",
          this.producto
        );
        this.mensajeGuardado = editar.data.mensaje;
        this.getProductos();
      } else {
        console.log("este es el producto", this.producto);
        this.getProductos();
      }
    },


    validarNombre(e, index, lon) {
      const regex = new RegExp(`^[a-zA-Z]{0,${lon}}$`, 'g');
      this.rojitoClientes = false

      if(!regex.test(`${this.formProductos[index]}${e.key}`)) {
        this.rojitoClientes = true
        e.preventDefault()
        return false
      }
      return true
    },

    validarCodigo(e, index, lon) {
      const regex = new RegExp(`^[A-Za-z0-9\s]{0,${lon}}$`, 'g');
      this.rojitoClientes = false
      if(!regex.test(`${this.formProductos[index]}${e.key}`)) {
        this.rojitoClientes = true
        e.preventDefault()
        return false
      }
      return true
    }

  },
  computed: {},
});
