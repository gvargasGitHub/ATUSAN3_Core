/**
 * 
 */
class Controller {
  constructor(name) {
    this.name = name;
  }
};
/**
 * Application
 */
class Application extends Controller {
  constructor(name) {
    super(name);
  }

  execute() {
    if (typeof this.onOpen == "function") this.onOpen();

    if (!Module.active()) throw new Error('No existe módulo.');

    ats.attachModule(Module.active());

    ats.stopLoader();
  }
};
/**
 * Module
 */
class ModuleBase extends Controller {
  /**
   * 
   * @param {String} name 
   */
  constructor(name) {
    super(name);
    ats.info(`Creando módulo ${name}`);
    this.components = [];
  }

  static active() {
    return window[__ModuleActive__];
  }

  registerResizeListener() {
    window.addEventListener("resize", this.onResize);
    // Ejecuta por primera vez el método
    this.onResize();
  }

  unregisterResizeListener() {
    window.removeEventListener("resize", this.onResize);
  }

  onOpen() {
    // TODO
  }

  onActivate() {
    return false;
  }

  onResize() {
    // TODO
  }

  addComponent(name) {
    this.components.push(name);
  }

  initComponents() {
    ats.info(`initComponents de ${this.name}:${this.components.length}`);
    this.components.forEach(componentName => window[componentName].init());
  }
  /**
   * Sends a request to the server.
   * @param {string} route 
   * @param {object} options 
   */
  send(route, options) {

    var fd = new FormData();

    fd.append('module', this.name);
    if (typeof options.data == "object") {
      for (const key in options.data) {
        if (!Object.hasOwn(options.data, key)) continue;

        fd.append(key, options.data[key]);
      }
    }
    if (typeof options.onDone == "undefined")
      throw new Error('Debe definir options.onDone como función.');

    if (typeof options.onFail == "undefined")
      options.onFail = rs => {
        alert(rs.message);
        console.warn(rs.detail);
      };

    var headers = (fd.has('csrf_token')) ? { 'X-CSRF-TOKEN': fd.get('csrf_token') } : {};

    route = ats.parseRoute(route);

    ats.startLoader();
    
    // Note: Se reemplaza $.ajax por ats.post para unificar la forma de enviar solicitudes al servidor.
    ats.post({
      url: BASE_URL + route,
      headers,
      data: fd
    }, {
      onDone: options.onDone,
      onFail: options.onFail
    });
  }
};
/**
 * @var {string} __ModuleActive__
 * Almacena el nombre del objeto "Module" presente.
 * Esta variable se actualiza en el "constructor"
 * de la clase "Module".
 */
var __ModuleActive__ = undefined;

/**
 * Module
 */
class Module extends ModuleBase {
  /**
   * 
   * @param {String} name 
   */
  constructor(name) {
    super(name);
    __ModuleActive__ = this.name;
  }
};

class ModuleNested extends ModuleBase { };

