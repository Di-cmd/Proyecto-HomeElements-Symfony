new Vue({
  el: "#app",
  delimiters: ["${", "}"],
  data: {
    formPedidos: {
      id: 0,
      codigo: "",
      departamento: "",
      municipio: "",
      productoSeleccionado: '',
      cantidadProducto: 0,
      productoAgregado:[],
    },
    productos: [],
    dane: [],
    departamentos: [],
    municipios: [],
    estadoEditar: 0,
    estadoId: 0,
    pedido: 0,
    pedidos: [],
    mensajeGuardado: "",
  },
  mounted() {
    this.getProductos();
    this.getPedidos();
    this.getClientes(), 
    this.getDepartamentos();
  },
  methods: {
    getProductos() {
      fetch(productoJSON)
        .then((response) => response.json())
        .then((response) => {
          this.productos = response.productos;
        });
    },

    async getPedidos() {
      let pedidos = await axios.get("http://127.0.0.1:8080/pedidoJSON");
      this.pedidos = pedidos.data.pedidos;
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
      let crearPedido = await axios.post(
        "http://127.0.0.1:8080/crearPedido",
        this.formPedidos
      );
      this.mensajeGuardado = crearPedido.data.mensaje;
      this.getPedidos();
    },

    async getClientes() {
      let clientes = await axios.get("http://127.0.0.1:8080/clienteJSON");
      this.clientes = clientes.data.clientes;
    },

    async editPedido(estado, idPedido, pedido) {
      this.estadoEditar = estado;
      this.estadoId = idPedido;
      this.pedido = pedido;

      if (this.pedido != 0) {
        let editar = await axios.post(
          "http://127.0.0.1:8080/editarPedido",
          this.pedido
        );
        this.mensajeGuardado = editar.data.mensaje;
        this.getPedidos();
      } else {
        console.log("este es el cliente", this.pedido);
        this.getPedidos();
      }
    },

    //Para el metodo eliminar Tengo que mandar el parametro, por medio de la ruta
    async deletePedido(id) {
      console.log(id)
      let eliminar = await axios.post("http://127.0.0.1:8080/deletePedido/"+id);
      eliminar = await eliminar;
      eliminar = eliminar.data;
      this.mensajeGuardado = eliminar["mensaje"];
      this.getPedidos();
    },


    async agregarProducto() {

      let Pagregado={
        idProducto:this.formPedidos.productoSeleccionado.id,
        nombreProducto:this.formPedidos.productoSeleccionado.nombre,
        cantidadProducto:this.formPedidos.cantidadProducto
      }
      this.formPedidos.productoAgregado.push(Pagregado);
  

      console.log(this.formPedidos.productoAgregado);
 
    },

    


    async eliminarProducto(producto) {
 
      this.formPedidos.productoAgregado.splice(producto.id,1);
      console.log(this.formPedidos.productoAgregado)
    },






  },




  computed: {},
});
