new Vue({
  el: "#app",
  delimiters: ["${", "}"],
  data: {
    formPedidos: {
      id: 0,
      codigo: "",
      departamento: "",
      municipio: "",
    },


    estadoEditar: 0,
    estadoId: 0,
    pedido: 0,
    pedidos: [],
    mensajeGuardado: "",
  },
  mounted() {
    this.getProductos();
    this.getPedidos();
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

    async crearPedido() {
      let crearPedido = await axios.post(
        "http://127.0.0.1:8080/crearPedido",
        this.formPedidos
      );
      this.mensajeGuardado = crearPedido.data.mensaje;
      this.getPedidos();
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
      let eliminar = await axios("http://127.0.0.1:8080/deletePedido/" + id, {
        method: "POST",
      });
      eliminar = await eliminar;
      eliminar = eliminar.data;
      this.mensajeGuardado = eliminar["mensaje"];
      this.getPedidos();
    },
  },
  computed: {},
});
