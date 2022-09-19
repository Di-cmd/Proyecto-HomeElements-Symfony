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
    conFormato:0
  },
  mounted() {
    this.getProductos();
  },
  methods: {
    async nuevoProducto(id) {
      this.nuevoP = id;

      this.formProductos.nombre = "";
      this.formProductos.codigo = 0;
      this.formProductos.cantidad = 0;
      this.formProductos.precio = 0;
      this.formProductos.categoria = "";
      this.formProductos.estado = "";
    },

    async getProductos() {
      let productos = await axios.get("productoJSON");
      this.productos = productos.data.productos;
      this.conFormato=98652;

      console.log(new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(this.conFormato));
      this.conFormato=new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(this.conFormato);
      // expected output: "123.456,79 €"

    },

    async crearProducto() {
      if (
        this.formProductos.nombre != "" &&
        this.formProductos.codigo != 0 &&
        this.formProductos.cantidad != 0 &&
        this.formProductos.precio != 0 &&
        this.formProductos.categoria != "" &&
        this.formProductos.estado != ""
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
      if (
        producto.nombre != "" &&
        producto.codigo != 0 &&
        producto.cantidad != 0 &&
        producto.precio != 0 &&
        producto.categoria != "" &&
        producto.estado != ""
      ) {
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
          this.getProductos();
        }
      } else {
        alert("Por favor debe diligenciar todos los campos");
      }
    },

    validarNombre(e, index, lon) {
      const regex = new RegExp(`^[A-Za-z- \s]{0,${lon}}$`, "g");
      this.rojitoClientes = false;

      if (!regex.test(`${this.formProductos[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }
      return true;
    },

    validarCodigo(e, index, lon) {
      const regex = new RegExp(`^[A-Za-z0-9\s]{0,${lon}}$`, "g");
      this.rojitoClientes = false;
      if (!regex.test(`${this.formProductos[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }
      return true;
    },

    validarCantidad(e, index, lon) {
      console.log(e);
      const regex = new RegExp(`^[0-9]{0,${lon}}$`, "g");
      this.rojitoClientes = false;

      if (!regex.test(`${this.formProductos[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }
      return true;
    },

    // Validaciones del editar:

    validarNombreEditar(e, producto, index, lon) {
      const regex = new RegExp(`^[A-Za-z- \s]{0,${lon}}$`, "g");
      this.rojitoClientes = false;

      if (!regex.test(`${producto[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }
      return true;
    },

    validarCodigoEditar(e, producto, index, lon) {
      const regex = new RegExp(`^[A-Za-z0-9\s]{0,${lon}}$`, "g");
      this.rojitoClientes = false;
      if (!regex.test(`${producto[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }
      return true;
    },

    validarCantidadEditar(e, producto, index, lon) {
      const regex = new RegExp(`^[0-9]{0,${lon}}$`, "g");
      this.rojitoClientes = false;

      if (!regex.test(`${producto[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }
      return true;
    },


    
  },
  computed: {

    formatoNumeroPrecioProducto: () => (precio) => {
      return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'COP' }).format(precio);
    }
  },
});
