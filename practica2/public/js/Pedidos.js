new Vue({
  el: "#app",
  delimiters: ["${", "}"],
  data: {
    formPedidos: {
      id: 0,
      codigo: "",
      departamento: "",
      municipio: "",
      cliente: "",
      productoSeleccionado: "",
      cantidadProducto: 0,
      totalPedido: 0,
      productoAgregado: [],
    },
    productos: [],
    clientes: [],
    dane: [],
    departamentos: [],
    municipios: [],
    estadoEditar: 0,
    estadoId: 0,
    pedido: 0,
    pedidos: [],
    mensajeGuardado: "",
    precioUnitario: 0,
    precioTotalProductos: 0,
    disponible: true,
    clienteEscogido: "",
    productosAgregados: [],
    pedidoModal: {},
    existeProducto: false,
  },
  mounted() {
    this.getClientes(), this.getDepartamentos(), this.getProductos();
  },

  methods: {
    async getProductos() {
      let productos = await axios.get(productoJSON);
      for (i = 0; i < productos.data.productos.length; i++) {
        if (
          productos.data.productos[i].estado == "Activo" &&
          productos.data.productos[i].cantidad > 0
        ) {
          this.productos.push(productos.data.productos[i]);
        }
      }
    },

    async getPedidos() {
      let pedidos = await axios.post(
        "http://127.0.0.1:8080/pedidoJSON/" + this.formPedidos["cliente"]
      );
      this.pedidos = pedidos.data.pedidos.map((pedido) => {
        return {
          ...pedido,
          editar: false,
        };
      });
      this.clienteEscogido = this.clientes.find(
        (cliente) => cliente.id === this.formPedidos["cliente"]
      );
    },

    async getDepartamentos() {
      let departamentos = await axios.get(
        "https://www.datos.gov.co/resource/xdk5-pm3f.json"
      );
      this.dane = departamentos.data;

      this.dane.forEach((dane) => {
        let filtro = this.departamentos.filter(
          (item) => item.codigo == dane.c_digo_dane_del_departamento
        );
        if (filtro.length == 0) {
          let data = {
            codigo: dane.c_digo_dane_del_departamento,
            nombre: dane.departamento,
          };
          this.departamentos.push(data);
        }
      });
    },

    async crearPedido() {
      if (
        this.formPedidos.codigo != "" &&
        this.formPedidos.departamento != "" &&
        this.formPedidos.municipio != "" &&
        this.formPedidos.productoAgregado.length > 0
      ) {
        this.formPedidos.totalPedido = this.formPedidos.productoAgregado.reduce(
          (prev, curr) => prev + curr.precioTotal,
          0
        );

        let crearPedido = await axios.post(
          "http://127.0.0.1:8080/crearPedido",
          this.formPedidos
        );
        this.mensajeGuardado = crearPedido.data.mensaje;
        this.getPedidos();

        //Limpiar el formulario
        this.formPedidos.codigo = "";
        this.formPedidos.departamento = "";
        this.formPedidos.municipio = "";
        this.formPedidos.productoSeleccionado = "";
        this.formPedidos.cantidadProducto = 0;
        this.formPedidos.totalPedido = 0;
        this.formPedidos.productoAgregado = [];
      } else {
        alert("Por favor debe diligenciar todos los campos");
      }
    },

    async getClientes() {
      let clientes = await axios.get("http://127.0.0.1:8080/clienteJSON");
      for (i = 0; i < clientes.data.clientes.length; i++) {
        if (clientes.data.clientes[i].estado == "Activo") {
          this.clientes.push(clientes.data.clientes[i]);
        }
      }
    },

    async editarPedido(index) {
      await this.getPedidos();
      this.pedidos[index].editar = true;
    },

    async guardarEdicion(pedido) {
      if (
        pedido.codigo != "" &&
        pedido.departamento != "" &&
        pedido.municipio != ""
      ) {
        let editar = await axios.post(
          "http://127.0.0.1:8080/editarPedido",
          pedido
        );
        this.mensajeGuardado = editar.data.mensaje;
        this.getPedidos();
      } else {
        alert("Por favor debe diligenciar todos los campos");
      }
    },

    // async editPedido(estado, idPedido, pedido) {
    //   this.estadoEditar = estado;
    //   this.estadoId = idPedido;
    //   this.pedido = pedido;

    //   if (this.pedido != 0) {
    //     let editar = await axios.post(
    //       "http://127.0.0.1:8080/editarPedido",
    //       this.pedido
    //     );

    //     this.mensajeGuardado = editar.data.mensaje;
    //     this.getPedidos();

    //   }
    //   else {
    //     console.log("este es el cliente", this.pedido);
    //     this.getPedidos();
    //   }
    // },

    //Para el metodo eliminar Tengo que mandar el parametro, por medio de la ruta
    async deletePedido(id) {
      let eliminar = await axios.post(
        "http://127.0.0.1:8080/deletePedido/" + id
      );

      eliminar = await eliminar;
      eliminar = eliminar.data;
      this.mensajeGuardado = eliminar["mensaje"];
      this.getPedidos();
    },

    async agregarProducto(nombreProducto) {
      // console.log(this.productosAgregados.map(agregado=>{
      //   if(agregado.nombre==nombreProducto.nombre){
      //      return{
      //       ...agregado,
      //       existe: true
      //     }
      //   }
      // }));

      if (
        this.formPedidos.productoSeleccionado.nombre != "" &&
        this.formPedidos.cantidadProducto > 0
      ) {
        let Pagregado = {
          idProducto: this.formPedidos.productoSeleccionado.id,
          nombreProducto: this.formPedidos.productoSeleccionado.nombre,
          cantidadProducto: this.formPedidos.cantidadProducto,
          precioProducto: this.precioUnitario,
          precioTotal: this.precioTotalProductos,
        };

        //Guardo los Productos y agrego los productos a una variable reactiva
        this.formPedidos.productoAgregado.push(Pagregado);
        this.productosAgregados.push(Pagregado);

        //Se limpia el formulario:
        this.formPedidos.productoSeleccionado = "";
        this.formPedidos.cantidadProducto = 0;
        this.precioUnitario = 0;
        this.precioTotalProductos = 0;
      } else {
        alert("Por favor debe diligenciar todos los campos");
      }
    },

    async eliminarProducto(producto) {
      this.formPedidos.productoAgregado.splice(producto.id, 1);
    },

    async precioProducto() {
      this.precioUnitario = this.formPedidos.productoSeleccionado.precio;
      this.precioTotalProductos =
        this.precioUnitario * this.formPedidos.cantidadProducto;
    },

    async modalProductos(pedido) {
      this.pedidoModal = pedido;
      let pedidos = await axios(
        "http://127.0.0.1:8080/buscarProductos/" + pedido.id
      );
      this.mensajeGuardado = pedidos.data.productosAsociados;
      this.productosAgregados = this.mensajeGuardado;
    },

    validarNombre(e, index, lon) {
      const regex = new RegExp(`^[a-zA-Z]{0,${lon}}$`, "g");
      this.rojitoClientes = false;

      if (!regex.test(`${this.formPedidos[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }

      return true;
    },

    validarCodigo(e, index, lon) {
      const regex = new RegExp(`^[A-Za-z0-9\s]{0,${lon}}$`, "g");
      this.rojitoClientes = false;
      if (!regex.test(`${this.formPedidos[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }

      return true;
    },

    validarCodigoEditar(e, pedido, index, lon) {
      const regex = new RegExp(`^[A-Za-z0-9\s]{0,${lon}}$`, "g");
      this.rojitoClientes = false;
      if (!regex.test(`${pedido[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }

      return true;
    },

    validarCantidad(e, index) {
      const regex = new RegExp(
        `^[0-9]{0,${this.formPedidos.productoSeleccionado.cantidad}}$`,
        "g"
      );
      this.rojitoClientes = false;

      if (!regex.test(`${this.formPedidos[index]}${e.key}`)) {
        this.rojitoClientes = true;
        e.preventDefault();
        return false;
      }
      return true;
    },

    cambioDepartamentoEditar(cliente) {
      cliente.municipio = "";
    },

    cambioDepartamento() {
      this.formPedidos.municipio = "";
    },

    plantilla() {

      productosPorPedido=this.pedidoModal;
      productosPorPedido=this.productosAgregados.map(producto=>{
        return{
          ...producto,
          cliente:this.clienteEscogido.nombre,
          codigoPedido:this.pedidoModal.codigo,
          departamento:this.pedidoModal.departamento,
          municipio:this.pedidoModal.municipio,
        }
      })
      let plantilla = XLSX.utils.book_new();
      plantilla.SheetNames.push("Plantilla");
      const info = [
        ['Codigo del Prodcuto', 'Nombre del Producto', 'precio por Unidad', 'Cantidad del producto', 'precio Total', 'Cliente', 'Codigo del Pedido', 'Departamento','Municipio']
    ]
      for (index in productosPorPedido) {
        aux = []
        for (propiedad in productosPorPedido[index]) {
            aux.push(productosPorPedido[index][propiedad])
        }
        info.push(aux)
    }
      const hoja = XLSX.utils.aoa_to_sheet(info);
      plantilla.Sheets["Plantilla"] = hoja;
      return XLSX.writeFile(plantilla, "PlantillaPedidos.xlsx");
    
    
    },



  },




  computed: {
    // this.formPedidos.totalPedido =this.formPedidos.totalPedido + Pagregado.precioTotal;

    totalPedido() {
      return this.formPedidos.productoAgregado.reduce((prev, curr) => {
        return prev + curr.precioTotal;
      }, 0);
    },

    productosSeleccionados() {
      return this.productos.filter((producto) => {
        let agregado = this.formPedidos.productoAgregado.filter(
          (agregado) => producto.nombre == agregado.nombreProducto
        );

        if (agregado.length == 0) {
          return true;
        }
        return false;
      });
    },
  },
});
