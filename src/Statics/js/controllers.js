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

  send(url, options) {

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
    ats.startLoader();
    
    $.ajax({
      // 3.0.9: Se complementa "url" para resolver implementaciones en producción.
      url: BASE_URL + url,
      method: 'POST',
      type: 'POST',
      processData: false,
      contentType: false,
      headers,
      data: fd
    })
      .done(rs => {
        // La respuesta obtenida tendrá la estructura:
        // {status, data, message, detail}
        try {
          rs = JSON.parse(rs); // disparará "Error" si es inválida.
          if (rs.status == 'ok') {
            options.onDone(rs.data);
          } else if (rs.status == 'error') {
            alert(rs.message);
            console.error(`${rs.message}\n${rs.detail}`);
          } else {
            options.onFail(rs.message);
          }
        } catch (e) {
          console.error(e.message);
          console.error(rs);
        }
      })
      .fail((xhr, status, error) => console.error(error))
      .always(() => {
        ats.stopLoader();
        ats.info('Transacción terminada');
      });
  }
};
/**
 * @var String __ModuleActive__
 * Almacena el nombre del objeto "Module" presente.
 * Esta variable se actualiza en el "constructor"
 * de la clase "Module".
 */
var __ModuleActive__ = undefined;

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

