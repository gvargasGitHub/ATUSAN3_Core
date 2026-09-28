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
    // v3.0.20: Se remplaza el código personalizado por el centralizado.
    this.send = ats.send;
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

