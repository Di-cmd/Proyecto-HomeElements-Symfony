new Vue({
  el: "#app",
  delimiters: ["${", "}"],
  data: {
    formClientes: {
      nombre: "",
      correo: "",
      departamento: "",
      municipio: "",
      estado:"",
    },
    rojitoClientes: false,
    dataModal: {
      cliente: {
        id: 0,
        nombre: "",
        correo: ""
      },
      pedido: [],
    },
    productos: [],
    clientes: [],
    dane: [],
    departamentos: [],
    municipios: [],
    region: [],
    estadoEditar: 0,
    estadoId: 0,
    cliente: 0,
    nuevoC: 0,
  },
  mounted() {
    this.getClientes(), 
    this.getDepartamentos();
  },
  methods: {
    async nuevoCliente(id) {
      this.nuevoC = id;
    },

    async getClientes() {
      let clientes = await axios.get("http://127.0.0.1:8080/clienteJSON");
      this.clientes = clientes.data.clientes;
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

    async crearCliente() {



      if (this.formClientes.nombre != "" && this.formClientes.municipio!= ""
      && this.formClientes.correo != "" && this.formClientes.departamento!=""
       && this.formClientes.estado !="" && this.formClientes.correo.length<64) {

      let crearCliente = await axios.post(
        "http://127.0.0.1:8080/crearCliente",
        this.formClientes
      );

      this.mensajeGuardado = crearCliente.data.mensaje;
      this.nuevoC = 0;
      this.getClientes();

      this.formClientes.nombre = "";
      this.formClientes.correo = "";
      this.formClientes.departamento = "";
      this.formClientes.municipio = "";

    } else {
      alert("Por favor debe diligenciar todos los campos");
    }

    },

    //Para el metodo eliminar Tengo que mandar el parametro, por medio de la ruta
    async deleteCliente(id) {
      let eliminar = await axios("http://127.0.0.1:8080/deleteCliente/" + id, {
        method: "POST",
      });

      eliminar = await eliminar;
      eliminar = eliminar.data;
      this.mensajeGuardado = eliminar["mensaje"];
      this.getClientes();
    },

    async editCliente(estado, idCliente, cliente) {
      this.estadoEditar = estado;
      this.estadoId = idCliente;
      this.cliente = cliente;

      if (this.cliente != 0) {
        let editar = await axios.post(
          "http://127.0.0.1:8080/editarCliente",
          this.cliente
        );

        this.mensajeGuardado = editar.data.mensaje;
        this.getClientes();
      } else {
        this.getClientes();
      }
    },

    async PedidoCliente(id) {
      let pedido = await axios.post(
        "http://127.0.0.1:8080/pedidoCliente/" + id
      );

      pedido = pedido.data;
      this.mensajeGuardado = pedido.data.mensaje;
      location.href = "http://127.0.0.1:8080/pedido";
    },

    async modalPedidos(cliente) {

      this.dataModal.cliente.id = cliente.id
      this.dataModal.cliente.nombre = cliente.nombre
      this.dataModal.cliente.correo = cliente.correo
      let pedidos = await axios("http://127.0.0.1:8080/pedidoJSON/"+this.dataModal.cliente.id);
      this.dataModal.pedido = pedidos.data.pedidos;
      console.log(this.dataModal.pedido);
    },

    validarNombre(e, index, lon) {
      const regex = new RegExp(`^[a-zA-Z]{0,${lon}}$`, 'g');
      this.rojitoClientes = false

      if(!regex.test(`${this.formClientes[index]}${e.key}`)) {
        this.rojitoClientes = true
        e.preventDefault()
        return false
      }
      
      return true
    },

    validarCorreo(e, index, lon) {
      const regex = new RegExp(`^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{0,${lon}}$`, 'g');
      this.rojitoClientes = false
      if(!regex.test(`${this.formClientes[index]}${e.key}`)) {
        this.rojitoClientes = true
        e.preventDefault()
        return false
      }
      
      return true
    }
  },

  computed: {},
});
