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
  /**
   * Invocado por evento DOMContentLoaded.
   * @param {string} name 
   */
  constructor(name) {
    super(name);
  }

  /**
   * Execute
   * Invocado por evento DOMContentLoaded.
   */
  execute() {
    if (typeof this.onOpen === "function") this.onOpen();

    if (!Module.active()) throw new Error('No existe módulo.');

    ats.attachModule(Module.active());
  }

  onOpen() {
    // TODO
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
    // ats.notify(`Creando módulo ${name}`);
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

  /**
   * Add Component.
   * @param {string} name 
   * 
   * Invocado por Component.constructor.
   */
  addComponent(name) {
    this.components.push(name);
  }

  /**
   * Init Components
   * Ejecuta Component::init de los Components cargados al módulo.
   * 
   * Invocado por Atusan.attachModule.
   */
  initComponents() {
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

    this.nested = [];
  }

  /**
   * Add Nested
   * @param {ModuleNested} nested
   */
  addNested(nested) {
    this.nested.push(nested);
  }
  /**
   * Init Nested
   */
  initNesteds() {
    this.nested.forEach(nested => ats.attachModule(nested));
  }
};

class ModuleNested extends ModuleBase {
  /**
   * 
   * @param {String} name 
   */
  constructor(name) {
    super(name);
    
    let mdparent = Module.active();

    if (mdparent && mdparent instanceof Module) {
      mdparent.addNested(this);
    }
  }
};

