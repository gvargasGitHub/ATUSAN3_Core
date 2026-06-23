/**
 * 
 */
class Component {
  /**
   * 
   * @param {String} name 
   * @param {String} owner
   * 
   * Cada "Component" requiere el nombre del "propietario" para:
   * a) Obtener el objeto "Module" activo.
   * b) Integrar el "Component" al diccionario de componentes del "Module".
   *    Esta integración permite a "Application.execute"/"atusan.attachModule" 
   *    ejecutar "module.initComponents" recorriendo dicho diccionario de 
   *    "Components" y ejecutar "Component.init".
   */
  constructor(name, owner) {
    this.name = name;
    this.element;

    ats.info(`Constuyendo ${name} de ${owner}`);

    if (typeof window[owner] == "undefined") console.error(`${owner} no existe.`);

    if (window[owner] instanceof ModuleBase) {
      this.owner = window[owner];
      this.owner.addComponent(this.name);
    } else {
      console.error(`${owner} no es clase Module.`);
    }
  }

  init() {
    ats.info(`Init ${this.name}`);
    this.element = document.getElementById(this.name);

    if (!this.element) throw new Error(`${this.name} no existe.`);
  }

  fitToParentHeight(margin = 0) {
    // valida la dimension de la pantalla
    if (screen.availWidth <= 768) return;

    if (this.element.offsetParent == null) return;
    // offset parent es the nearest ancestor, if not exists, then offset parent is
    // document body. The nearest ancestor of all Components is "content" block.
    // w3schools.com/jsref
    ats.info(`${this.name} ajustándose a  ${this.element.offsetParent.tagName}`);
    let h = this.element.offsetParent.offsetHeight - (this.element.offsetTop + margin);

    this.element.style.height = `${h}px`;
  }

  fitToParentWidth() {
    // valida la dimension de la pantalla
    if (screen.availWidth <= 768) return;

    if (this.element.offsetParent == null) return;
    // offset parent es the nearest ancestor, if not exists, then offset parent is
    // document body. The nearest ancestor of all Components is "content" block.
    // w3schools.com/jsref

    let w = this.element.offsetParent.offsetWidth - (this.element.offsetLeft + 10);
    ats.info(`width fitting ${this.name}:${w}px`);
    this.element.style.width = `${w}px`;
  }

  buildEventName(eventType) {
    return 'on' + eventType.replace(/^[a-z]/, x => x.toUpperCase());
  }
};
/** 
 * 
*/
class ButtonGroup extends Component {
  init() {
    super.init();

    const buttons = this.element.querySelectorAll('.ats-btn');
    buttons.forEach(btn => this[btn.id] = new Button(btn));
  }
};

class Button {
  constructor(button) {
    this.name = button.id;
    this.element = button;
  }

  set enable(onoff) {
    this.element.disabled = !onoff;
  }
};

/**
 * Navbar
 */
class Navbar extends Component {
  /**
   * 
   */
  init() {
    super.init();

    const items = this.element.querySelectorAll("li.itemClickEv");

    items.forEach(item => item.addEventListener('click', Navbar.handlerMenuItemEvent));

    const dropdowns = this.element.querySelectorAll("li.ddClickEv");

    dropdowns.forEach(item => item.addEventListener('click', Navbar.handlerDropdownEvent));

    const bars = this.element.querySelector("div.bars");

    bars.addEventListener('click', Navbar.handlerBarsEvent);
  }

  closeResponsiveView() {
    this.element.classList.remove('responsive');
    this.element.querySelector('.bars')
      .classList.remove('transform');
  }

  static handlerMenuItemEvent(ev) {
    let route = ev.target.getAttribute("ats-route"),
      name = ev.target.getAttribute("ats-itemname"),
      menu = ev.target.getAttribute("ats-menuname");

    if (!window[menu]) return;

    window[menu].onMenuItemClick({ name, route });

    // Cierra la vista responseive
    window[menu].closeResponsiveView();
  }

  /**
   * 
   */
  static handlerDropdownEvent(ev) {
    const elm = (ev.target.classList.contains('caret'))
      ? ev.target.parentElement : ev.target;

    elm.classList.toggle('show');

    ev.stopPropagation();
  }

  static handlerBarsEvent(ev) {
    ev.stopPropagation();

    const elm = (ev.target.classList.contains("bars"))
      ? ev.target : ev.target.parentElement;

    window[elm.getAttribute("ats-menuname")].element.classList.toggle("responsive");
    elm.classList.toggle("transform");
  }

  onMenuItemClick(item) {
    // TODO
  }
};

/**
 * Subnavbar
 */
class Subnavbar extends Component {
  /**
   * 
   */
  init() {
    super.init();

    const items = this.element.querySelectorAll("a.itemClickEv");

    items.forEach(item => item.addEventListener('click', Subnavbar.handlerMenuItemEvent));

    const dropdowns = this.element.querySelectorAll("div.ddClickEv");

    dropdowns.forEach(item => item.addEventListener('click', Subnavbar.handlerDropdownEvent));

    const bars = this.element.querySelector("div.bars");

    bars.addEventListener('click', Subnavbar.handlerBarsEvent);
  }

  closeResponsiveView() {
    this.element.classList.remove('responsive');
    this.element.querySelector('.bars')
      .classList.remove('transform');
  }

  static handlerMenuItemEvent(ev) {
    ev.preventDefault();

    let route = ev.target.getAttribute("ats-route"),
      name = ev.target.getAttribute("ats-itemname"),
      menu = ev.target.getAttribute("ats-menuname");

    if (!window[menu]) return;

    window[menu].onMenuItemClick({ name, route });

    // Cierra la vista responseive
    window[menu].closeResponsiveView();
  }

  /**
   * 
   */
  static handlerDropdownEvent(ev) {
    const elm = (ev.target.classList.contains('caret'))
      ? ev.target.parentElement : ev.target;

    elm.classList.toggle('show');

    ev.stopPropagation();
  }

  static handlerBarsEvent(ev) {
    ev.stopPropagation();

    const elm = (ev.target.classList.contains("bars"))
      ? ev.target : ev.target.parentElement;

    window[elm.getAttribute("ats-menuname")].element.classList.toggle("responsive");
    elm.classList.toggle("transform");
  }

  onMenuItemClick(item) {
    // TODO
  }
};
/**
 * Sidebar
 */
class Sidebar extends Component {
  /** */
  init() {
    super.init();

    const items = this.element.querySelectorAll("li.itemClickEv");

    items.forEach(item => item.addEventListener('click', Sidebar.handlerMenuItemEvent));

    const dropdowns = this.element.querySelectorAll("li.ddClickEv");

    dropdowns.forEach(item => item.addEventListener('click', Sidebar.handlerDropdownEvent));

    const bars = this.element.querySelector("div.bars");

    bars.addEventListener('click', Sidebar.handlerBarsEvent);
  }

  closeResponsiveView() {
    this.element.classList.remove('responsive');
    this.element.querySelector('.bars')
      .classList.remove('transform');
  }

  static handlerMenuItemEvent(ev) {
    ats.hideNavDropdown();
    let route = ev.target.getAttribute("ats-route"),
      name = ev.target.getAttribute("ats-itemname"),
      menu = ev.target.getAttribute("ats-menuname");

    if (!window[menu]) return;

    window[menu].onMenuItemClick({ name, route });

    // Cierra la vista responseive
    window[menu].closeResponsiveView();
  }

  static handlerDropdownEvent(ev) {
    const elm = (ev.target.classList.contains('caret'))
      ? ev.target.parentElement : ev.target;

    elm.classList.toggle('show');

    ev.stopPropagation();
  }

  static handlerBarsEvent(ev) {
    ev.stopPropagation();

    const elm = (ev.target.classList.contains("bars"))
      ? ev.target : ev.target.parentElement;

    window[elm.getAttribute("ats-menuname")].element.classList.toggle("responsive");
    elm.classList.toggle("transform");
  }

  onMenuItemClick(item) {
    // TODO
  }

  fitToParentHeight(margin = 0) {
    // valida la dimension de la pantalla
    if (screen.availWidth <= 768) return;

    if (this.element.offsetParent == null) return;
    // offset parent es the nearest ancestor, if not exists, then offset parent is
    // document body. The nearest ancestor of all Components is "content" block.
    // w3schools.com/jsref

    let h = this.element.offsetParent.offsetHeight - ((this.element.offsetTop + 10) - margin);
    ats.info(`height fitting ${this.name}:${h}px`);
    this.element.style.height = `${h}px`;
  }

  fitToParentWidth() {
    // valida la dimension de la pantalla
    if (screen.availWidth <= 768) return;

    if (this.element.offsetParent == null) return;
    // offset parent es the nearest ancestor, if not exists, then offset parent is
    // document body. The nearest ancestor of all Components is "content" block.
    // w3schools.com/jsref

    let w = this.element.offsetParent.offsetWidth - (this.element.offsetLeft + 10);
    ats.info(`width fitting ${this.name}:${w}px`);
    this.element.style.width = `${w}px`;
  }
};

/**
 * DataViewBase
 */
class DataViewBase extends Component {
  init() {
    super.init();
  }

  static getControlId(element) {
    let id = element.id.split("-");

    let row = (typeof id[2] !== "undefined") ? Number(id[2]) : 1;

    return {
      view: id[0],
      control: id[1],
      row
    }
  }
};

/**
 * DataForm
 */
class DataForm extends DataViewBase {
  init() {
    super.init();
    this.entries = [];
    this.maplists = new Map();
    this.load();
  }

  load() {
    this.maplists.clear();

    const form = this.element.querySelector(".ats-dataform form");

    form.addEventListener("submit", DataForm.handlerFormSubmitEvent);
    form.addEventListener("reset", DataForm.handlerFormResetEvent);

    // Alimenta colección de controles
    const controls = this.element.querySelectorAll('input, select, textarea');

    controls.forEach(el => {
      if (el.hasAttribute('name')) this.entries.push(el.getAttribute('name'));
    });

    // Actualiza valor para "checkbox"
    const checks = this.element.querySelectorAll("input[type='checkbox']");
    checks.forEach(el => el.addEventListener("change", ev => ev.target.value = ev.target.checked ? 1 : 0));

    // Inputs
    const inputs = this.element.querySelectorAll(".inputEv");

    inputs.forEach(el => {
      el.addEventListener("input", DataForm.handlerControlEvent);
    });

    const files = this.element.querySelectorAll(".file");
    files.forEach(el => {
      // esto es para resolver bug de FireFox
      el.addEventListener('focus', () => el.classList.add('has-focus'));
      el.addEventListener('blur', () => el.classList.remove('has-focus'));
    });

    const selects = this.element.querySelectorAll(".changeEv");

    selects.forEach(el => {
      el.addEventListener("change", DataForm.handlerControlEvent);
      // alimenta la colección  de controles Select
      let { control } = DataViewBase.getControlId(el);

      // integra el control en mapa de listados
      if (!this.maplists.has(control)) this.maplists.set(control, ControlBase.instance(el.type.toLowerCase(), this, control, el));

    });

    // Keydown
    const keydowns = this.element.querySelectorAll(".keydownEv");

    keydowns.forEach(el => el.addEventListener("keydown", DataForm.handlerControlEvent));

    // Controles Autocomplete
    const autocompletes = this.element.querySelectorAll(".completeEv");
    // funcionalidad para sujetar movilidad en listado de items
    autocompletes.forEach(el => {
      el.addEventListener("keydown", ControlAutoComplete.handlerKeyDown);
      // alimenta la colección de controles Autocomplete
      let { control } = DataViewBase.getControlId(el);

      if (!this.maplists.has(control)) this.maplists.set(control, ControlBase.instance('autocomplete', this, control, el));
    });


    this.onPopulateDone();
  }

  disableItem(control) {
    const elms = this.findByName(control);

    if (elms.length == 0) throw new Error(`${control} no existe en ${this.name}`);

    elms.forEach(el => el.disabled = true);
  }

  enableItem(control) {
    const elms = this.findByName(control);

    if (elms.length == 0) throw new Error(`${control} no existe en ${this.name}`);

    elms.forEach(el => el.disabled = false);
  }

  feedList(control, list) {
    const elms = this.findByName(control);

    if (elms.length == 0) throw new Error(`${control} no existe en ${this.name}`);

    if (this.maplists.has(control)) {
      this.maplists.get(control).feedList(list);
    } else
      throw new Error(`${control} no es una lista desplegable`);
  }

  inflate(content) {
    const body = this.element.querySelector(".body");
    while (body.hasChildNodes()) body.removeChild(body.firstChild);

    body.innerHTML = content;

    this.load();

    this.onPopulateDone();
  }

  getData() {
    var data = {};

    this.entries.forEach(c => data[c] = this.getItem(c));

    return data;
  }

  getItem(control) {
    const elms = this.findByName(control);

    if (elms.length == 0) throw new Error(`${control} no existe en ${this.name}`);

    let type = elms[0].getAttribute("type");

    let resolver = {
      file: el => {
        let files = [];
        for (let i = 0; i < el[0].files.length; i++) files.push(el[0].files[i].name);

        return files.join(", ");
      },
      radio: el => {
        let value;
        el.forEach(r => {
          if (r.checked) value = r.value
        });
        return value;
      }
    };

    switch (type) {
      case 'file':
      case 'radio':
        return resolver[type](elms);
      default:
        return elms[0].value;
    }
  }

  setItem(control, value) {
    const elms = this.findByName(control);

    if (elms.length == 0) throw new Error(`${control} no existe en ${this.name}`);

    let type = elms[0].getAttribute("type");

    switch (type) {
      case 'radio':
        elms.forEach(r => r.checked = (r.value == value));
        break;
      case 'file':
        break;
      case 'checkbox':
        elms[0].checked = (Number(value) == 1);
      default:
        elms[0].value = value;
    }
  }

  setFocus(control) {
    const elms = this.findByName(control);

    if (elms.length == 0) throw new Error(`${control} no existe en ${this.name}`);

    elms[0].focus();
  }

  findByName(control) {
    return this.element.querySelectorAll(`[name^="${control}"]`);
  }

  reset() {
    this.element.querySelector(`#${this.name}-form`).reset();
  }

  lists() {
    return this.maplists;
  }

  static handlerControlEvent(ev) {

    let { view, control } = DataViewBase.getControlId(ev.target);

    let value = window[view].getItem(control);

    let eventName = 'on' + ev.type.replace(/^[a-z]/, x => x.toUpperCase());
    // Ejecuta el evento
    window[view][eventName]({ control, value });

    // Muestra en la "etiqueta" el o los nombres de los archivos
    if (ev.target.type == "file")
      window[view].maplists.get(control).feedList(window[view].element.querySelector(`input[name^="${control}"]`).files);
  }

  static handlerFormSubmitEvent(ev) {
    ats.info(`Deteniendo a ${ev.type}`);
    ev.preventDefault();

    let { view } = DataViewBase.getControlId(ev.target);
    let route = ev.target.getAttribute("ats-route");

    // Uso de FormData. Nota: Incluye elementos "type=file".
    var fd = new FormData(ev.target);
    // Complementa "fd" con elementos perdidos (ej: checkbox value=0)
    var data = window[view].getData();
    for (let p in data) if (!fd.has(p)) fd.append(p, data[p]);

    var headers = (fd.has('csrf_token')) ? { 'X-CSRF-TOKEN': fd.get('csrf_token') } : {};
    
    $.ajax({
      // 3.0.9: Se complementa "url" para resolver implementaciones en producción.
      url: BASE_URL + route,
      method: 'POST',
      type: 'POST',
      processData: false,
      contentType: false,
      headers,
      data: fd
    })
      .done(rs => {
        try {
          rs = JSON.parse(rs);
          if (rs.status == 'ok')
            window[view].onSubmitDone(rs.data);
          else
            window[view].onSubmitFail(rs);
        } catch (e) {
          console.error(e.message);
          console.error(rs);
        }
      })
      .fail((xhr, status, error) => console.error(error))
      .always(() => ats.info('Transacción terminada'));
  }

  static handlerFormResetEvent(ev) {
    ats.info(`${ev.type} ha reestablecido el formulario`);
  }

  onInput(ar) {
    // TO DO
  }

  onChange(ar) {
    // TO DO
  }

  onKeydown(ar) {
    // TO DO
  }

  onPopulateDone() {
    // TO DO
  }

  onSubmitDone(rs) {
    // TO DO
  }

  onSubmitFail(err) {
    // TO DO
    alert(err.message);
    console.error(err.detail);
  }
};

/**
 * 
 */
class DataMultiForm extends DataForm {
  constructor(name, owner) {
    super(name, owner);

    // Indice correspondiente al formulario final
    this.finalForm = 0;
    // Indice correspondiente al formulario presente
    this.currentForm = 0;
    // Bloqueo para cambiar de formulario
    this.locked = true;

  }
  init() {
    super.init();
  }

  load() {
    super.load();

    this.finalForm = this.element.querySelectorAll(".stepform").length - 1;

    this.showFormByIndex(this.currentForm);

    this.onCurrentForm({ index: this.currentForm, command: 'load' });
  }

  showFormByIndex(index) {
    const steps = this.element.querySelectorAll(".stepform");

    steps[this.currentForm].style.display = "none";
    steps[index].style.display = "block";

    this.disableButton('backward', (index == 0));
    this.disableButton('forward', (index == this.finalForm));
    this.disableButton('finish', (index != this.finalForm));

    this.updateStepMarks(index);

    // Actualiza el valor de "currentForm"
    this.currentForm = index;
  }

  disableButton(btnName, disable) {
    this.element.querySelector(`#${this.name}_${btnName}`).disabled = disable;
  }

  updateStepMarks(index) {
    const marks = this.element.querySelectorAll('.stepmark');
    marks.forEach((mark, i) => {
      if (i == index)
        mark.classList.add('active');
      else
        mark.classList.remove('active');
    });
  }

  static handlerButtonEvent(ev) {
    ev.preventDefault();

    window[ev.target.getAttribute('ats-form')].processButton(ev.target.getAttribute('ats-command'));
  }

  processButton(command) {
    let index = 0;
    let current = this.currentForm;

    // activa bloqueo
    this.locked = true;

    // establece el indice del nuevo "form"
    if (command == 'forward')
      index = this.currentForm + 1;
    else if (command == 'backward')
      index = this.currentForm - 1;
    else if (command == 'finish' || command == 'submit')
      index = this.finalForm;

    try {
      // Invoca "allowContinue".
      // true: se moverá al siguiente formulario y bloqueará el uso de funcionalidad "proceed".
      // false: finalizará ejecución son moverse a otro formulario.
      this.locked = this.allowContinue({ current, index, command });

      // Valida que el valor de retorno sea booleano
      if (typeof this.locked != 'boolean') throw new Error('"allowContinue" debe retornar booleano.');

      // Si "allowContinue" retorna false, finaliza ejecución
      if (this.locked === false) return;

      // Muestra el formulario solicitado.
      // "showFormByIndex" actualiza el valor de "currentForm"
      this.showFormByIndex(index);

      this.ejecuteFormEvent(index, command);
    } catch (e) {
      console.error(e);
      this.locked = true;
    }
  }

  // Functionallity
  static eventDone(command) {
    // return 'on' + command.substr(0, 1).toUpperCase() + command.substr(1) + 'Done';
    // return 'on' + command.charAt(0).toUpperCase() + command.slice(1) + 'Done';
    return 'on' + command.replace(/^[a-z]/, x => x.toUpperCase()) + 'Done';
  }

  moveTo(index, command) {
    // Si el bloqueo esta activado, no realiza movimiento de formulario
    if (this.locked) return;

    this.showFormByIndex(index);

    this.ejecuteFormEvent(index, command);

    this.locked = true;
  }

  forward() {
    this.moveTo(this.currentForm + 1, 'forward');
  }
  backward() {
    this.moveTo(this.currentForm - 1, 'backward');
  }
  finish() {
    this.moveTo(this.finalForm, 'finish');
  }
  // Processors
  ejecuteFormEvent(index, command) {
    // Ejecuta "onCurrentForm"
    this.onCurrentForm({ index, command });

    if (command == 'finish')
      this.submit();
    else
      this[DataMultiForm.eventDone(command)]({ index, command });
  }

  //
  submit() {
    const form = this.element.querySelector(`#${this.name}-form`);

    if (form.requestSubmit)
      form.requestSubmit();
    else
      if (form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true })))
        form.submit();
  }

  // Events
  /**
   * Allow Continue
   * Permite continuar al siguiente formulario.
   * true : Se moverá al formulario solicitado.
   * false: Bloqueará el formulario.
   * @returns boolean
   */
  allowContinue(ar) {
    // TO DO
    return true;
  }

  onCurrentForm(ar) {
    // TO DO
  }

  onForwardDone(ar) {
    // TO DO
  }

  onBackwardDone(ar) {
    // TO DO
  }
};
/**
 * DataGrid
 */
class DataGrid extends DataViewBase {
  // constructor(name, owner) {
  //   super(name, owner);
  // }

  init() {
    super.init();

    this.load();
  }

  load() {
    this.controls = [];
    this.total_rows = 0;

    const view = this;
    //
    // Hiddens
    // Alimenta colección "controls"
    const hiddens = this.element.querySelectorAll('input[type="hidden"]');

    hiddens.forEach(el => {
      let { control } = DataViewBase.getControlId(el);
      if (typeof control == "undefined") return;

      if (view.controls.lastIndexOf(control) == -1) view.controls.push(control);
    });
    // TDs
    // Alimenta colección "controls"
    // Establece "total_rows" con el total de filas
    // Establece sujetador de evento onClick
    const tds = this.element.querySelectorAll("table>tbody.detail>tr>td.data");
    tds.forEach((el, i) => {
      let { control, row } = DataViewBase.getControlId(el);
      if (typeof control == "undefined") return;

      if (view.controls.lastIndexOf(control) == -1) view.controls.push(control);
      if (view.total_rows < row) view.total_rows = row;

      el.addEventListener("click", DataGrid.handlerTableDataEvent);
    });
    // TD + Menu Options
    const menus = this.element.querySelectorAll("td.menu-options");

    menus.forEach((el, i) => el.addEventListener("click", DataGrid.handlerTableDataEvent));

    //
    // Text, Password, Date, Time
    // Establece sujetador de evento onInput
    const inputs = this.element.querySelectorAll(".inputEv");

    inputs.forEach(el => el.addEventListener("input", DataGrid.handlerInputControl));
    //
    // Checkbox, Switch
    // Establece sujetador de evento onChange
    // Actualiza valor para "checkbox"
    const checks = this.element.querySelectorAll("input[type='checkbox']");
    checks.forEach(el => el.addEventListener("change", ev => ev.target.value = ev.target.checked ? 1 : 0));

    const changers = this.element.querySelectorAll(".changeEv");
    changers.forEach(el => el.addEventListener("change", DataGrid.handlerInputControl));
    //
    // MenuOptions
    // Establece sujetador de evento onClick para icono de MenuOptions
    const mopt = this.element.querySelectorAll("table>tbody.detail>tr>td.menu-options div.content a.option");

    mopt.forEach(el => el.addEventListener("click", DataGrid.handlerMenuOptionEvent));

    this.onPopulateDone();
  }

  inflate(content) {
    this.reset();

    const body = this.element.querySelector(".body");

    body.innerHTML = content;

    this.load();

    this.onPopulateDone();
  }

  getData(row) {
    if (row > this.total_rows || row == 0) return undefined;
    const view = this;
    let data = {};

    this.controls.forEach(control => {
      data[control] = view.getItem(row, control);
    });

    return data;
  }

  /** */
  getItem(row, control) {
    // obtiene la celda (td)
    const td = this.element.querySelector(`#${this.name}-${control}-${row}`);

    if (td == null) throw new Error(`${control}-${row} no existe en ${this.name}`);
    // en cada celda se ha definido el tipo de control
    let type = td.getAttribute("type");

    switch (type) {
      case 'data': return td.innerText;
      case 'hidden': return td.value;
      case 'checkbox':
      case 'basic':
      case 'select':
      case 'switch':
        return td.querySelector('input, select').value;
      case 'state': return td.getAttribute('value');
      default:
        return null;
    }
  }

  setItem(row, control, value) {
    const el = this.element.querySelector(`#${this.name}-${control}-${row}`);

    if (el == null) throw new Error(`${control}-${row} no existe en ${this.name}`);

    let type = el.getAttribute("type");

    switch (type) {
      case 'basic':
      case 'select':
        el.firstElementChild.value = value;
        break;
      case 'checkbox':
        el.firstElementChild.checked = (Number(value) == 1);
        el.firstElementChild.value = value;
        break;
      case 'hidden':
        el.value = value;
      case 'data':
      default:
        el.innerText = value;
    }
  }

  selectRow(row) {
    const trs = this.element.querySelectorAll("table tbody.detail tr.row");

    trs.forEach((tr, i) => {
      if (row == (i + 1))
        tr.classList.add("selected");
      else
        tr.classList.remove("selected");
    });
  }

  reset() {
    const body = this.element.querySelector(".body");
    while (body.hasChildNodes()) body.removeChild(body.firstChild);
  }

  /**
   * Table Data
   * @param {*} ev 
   */
  static handlerTableDataEvent(ev) {
    // console.log(`handlerTD:${ev.type} of ${ev.target.tagName}`);
    ev.stopImmediatePropagation();
    ats.hideMenuOptionsDropDown();

    // Evita procesar el evento para sub-elementos
    if (/(option|span)/i.test(ev.target.tagName)) return;

    let { view, control, row } = DataViewBase.getControlId(ev.target);

    let type = ev.target.getAttribute("type");

    // Acciones en base al tipo de celda
    if (type == 'menu') {
      // Muestra el menú de opciones. Obtiene el div.content
      let div;
      if (ev.target.tagName.toLowerCase() == 'td')
        div = ev.target.querySelector('div.content');
      else if (ev.target.tagName.toLowerCase() == 'i')
        div = ev.target.nextElementSibling;

      if (div != null) div.classList.toggle("show");
    }
    // Obtiene el valor presente en la celda
    let value = window[view].getItem(row, control);

    window[view].onClick({ control, row, value });
  }

  /**
   * Input Controls
   */
  static handlerInputControl(ev) {
    // console.log(`handlerInput:${ev.type} of ${ev.target.tagName}`);
    ev.stopPropagation();
    ats.hideMenuOptionsDropDown();

    let { view, control, row } = DataViewBase.getControlId(ev.target);

    let type = ev.target.getAttribute("type");

    let value;
    if (ev.target.getAttribute("type") == 'checkbox') {
      value = (ev.target.checked) ? 1 : 0;
    } else {
      value = ev.target.value;
    }

    let eventName = window[view].buildEventName(ev.type);

    window[view][eventName]({ control, row, value });
  }

  static handlerMenuOptionEvent(ev) {
    ev.stopPropagation();
    let view = ev.target.getAttribute("ats-owner"),
      control = ev.target.getAttribute("ats-name"),
      row = ev.target.getAttribute("ats-row");

    ats.hideMenuOptionsDropDown();

    window[view].onMenuOptionClick({
      control, row
    });
  }

  onClick(ar) {
    // TODO
  }

  onChange(ar) {
    // TODO
  }

  onInput(ar) {
    // TODO
  }

  onMenuOptionClick(ar) {
    // TODO
  }

  onPopulateDone() {
    // TODO
  }
};
/**
 * DataTree
 */
class DataTree extends DataViewBase {
  init() {
    super.init();

    this.load();
  }

  load() {
    const tds = this.element.querySelectorAll("td");

    tds.forEach((el, i) => {
      if (el.classList.contains("caret")) el.addEventListener('click', DataTree.handlerCaretEvent);

      if (el.classList.contains("clickEv")) el.addEventListener('click', DataTree.handlerControlEvent);
    });
    const checks = this.element.querySelectorAll("input[type=checkbox]");
    checks.forEach(el => el.addEventListener("change", ev => ev.target.value = ev.target.checked ? 1 : 0));

    const change = this.element.querySelectorAll(".changeEv");
    change.forEach(el => el.addEventListener('change', DataTree.handlerControlEvent));
  }

  getData(index) {
    const item = this.element.querySelector(`#${this.name}-${index}`);

    if (item == null) return {};

    let data = {};
    for (let i = 0; i < item.attributes.length; i++) {
      let attr = item.attributes[i];
      if (!/^data-/.test(attr.name)) continue;
      let name = attr.name.substring(5);
      data[name] = attr.value;
    }

    return data;
  }

  selectItem(i_index) {
    const tables = this.element.querySelectorAll("table");

    tables.forEach(table => {
      let { index } = DataTree.getControlId(table);

      if (index == i_index)
        table.classList.add("selected");
      else
        table.classList.remove("selected");
    });
  }

  unselectItems() {
    const tables = this.element.querySelectorAll("table");

    tables.forEach(table => table.classList.remove("selected"));
  }

  static getControlId(element) {
    let id = element.id.split("-");

    let control = (typeof id[2] !== "undefined") ? id[2] : '';
    let action = (typeof id[3] !== "undefined") ? id[3] : '';
    return {
      view: id[0],
      index: id[1],
      control,
      action
    }
  }

  static handlerCaretEvent(ev) {
    let { view, index } = DataTree.getControlId(ev.target);

    ev.target.classList.toggle("down");

    const content = window[view].element.querySelector(`#${view}-${index}-content`);

    if (content != null) {
      if (ev.target.classList.contains("down"))
        content.classList.add('show');
      else
        content.classList.remove('show');
    }

    ev.stopPropagation();
  }

  static handlerControlEvent(ev) {
    let { view, index, control, action } = DataTree.getControlId(ev.target);

    let type = ev.target.getAttribute('type');

    let getValue = {
      action: () => action,
      data: () => ev.target.innerText,
      checkbox: () => ev.target.value
    };
    let value = (getValue[type]) ? getValue[type]() : undefined;

    let eventName = window[view].buildEventName(ev.type);
    let level = ev.target.getAttribute('level');
    window[view][eventName]({ control, index, level, action, value });

    ev.stopPropagation();
  }

  onClick() {
    // TO DO
  }

  onChange() {
    // TO DO
  }
};
/**
 * Modal
 */
class Modal extends Component {
  // postConstructor() { }

  init() {
    super.init();

    const close = this.element.querySelector("i.close");

    close.addEventListener("click", Modal.handlerCloseModalEvent);
  }

  openModal(params) {
    this.element.style.display = "block";

    this.onOpen(params);
  }

  closeModal() {
    let close = this.onClose();

    if (typeof close == "undefined") close = true;

    if (close) this.element.style.display = "none";
  }

  onOpen() {
    // TODO
  }

  onClose() {
    // TODO
    return true;
  }

  static handlerCloseModalEvent(ev) {
    ev.stopPropagation();
    let modal = ev.target.getAttribute("ats-owner");

    window[modal].closeModal();
  }
};

/**
 * 
 */
class TabGroup extends Component {

  constructor(name, owner) {
    super(name, owner);

    this.contents = [];
    this._current;
  }

  init() {
    super.init();
    const btns = this.element.querySelectorAll(".buttons button");

    let first = true;
    btns.forEach(btn => {
      btn.addEventListener('click', TabGroup.handlerButtonEvent);

      let name = btn.getAttribute('ats-name');

      ats.info(`${name} de ${this.name} de ${this.owner.name}`);
      this[name] = new TabGroupContent(name, this.owner, this, btn);

      this.contents.push(name);

      if (first) {
        this[name].show();
        this.current = name;
        first = false;
      }
    });
  }

  /**
   * onClick
   * @param {String} ar
   */
  onClick(ar) {
    // TODO
  }

  /**
   * onChange
   * @param {Object} ar {String prior, String current}
   */
  onChange(ar) {
    // TODO
  }

  get current() {
    return this._current;
  }

  set current(x) {
    this._current = x;
  }

  /**
   * Select Content
   * @param {Button} button 
   */
  __selectContent(button) {
    let name = button.getAttribute('ats-name');

    this.contents.forEach(content => {
      if (content == name) {
        this[content].show();
        this.current = name;
      } else
        this[content].hide();
    });
  }

  /**
   * Open Module
   * @param {String} path 
   * @param {Object} data 
   */
  openModule(path, data) {
    if (typeof data == "undefined") data = {};

    this.owner.send(path, {
      data,
      onDone: module => {
        let button, content;

        let prior = this.current;

        let i = this.contents.findIndex(val => val == module.name);
        if (i === -1) {
          // No existe
          close = document.createElement("I");
          close.setAttribute("ats-owner", this.owner.name);
          close.setAttribute("ats-parent", this.name);
          close.setAttribute("ats-name", module.name);
          close.classList.add("close");
          close.addEventListener('click', TabGroup.handlerCloseEvent);


          button = document.createElement("BUTTON");
          button.setAttribute("ats-owner", this.owner.name);
          button.setAttribute("ats-parent", this.name);
          button.setAttribute("ats-name", module.name);
          button.classList.add("closeable");

          button.id = `${this.name}-${module.name}-button`;
          button.appendChild(document.createTextNode(module.title));
          button.appendChild(close);
          button.addEventListener('click', TabGroup.handlerButtonEvent);
          this.element.querySelector(".buttons")
            .append(button);

          content = document.createElement("DIV");
          content.id = `${this.name}-${module.name}`;
          content.classList.add("content");
          content.classList.add(this.name);

          this.__parseScripts(content, module.content);

          this.element.querySelector(".contents")
            .append(content);

          this[module.name] = new TabGroupContent(module.name, this.owner, this, button);

          this.contents.push(module.name);

          this.current = module.name;
        } else {
          // Existe
          button = this[module.name].button;
          this[module.name].update(module.content);
        }

        this.__selectContent(button);

        // Module onOpen Event
        if (window[module.name] == null) console.error(`${module.name} no fue creado.`);
        ats.attachModule(window[module.name]);

        // onChange Event
        if (prior != this.current && typeof this.onChange == "function") this.onChange({ prior, current: this.current });
      },
      onFail: rs => console.error(rs)
    });
  }

  /**
   * Parse Scripts
   * Generado por ChatGPT
   */
  __parseScripts(divContent, htmlString) {
    // 1. Crear contenedor temporal
    const temp = document.createElement('div');
    temp.innerHTML = htmlString;

    // 2. Extraer scripts
    const scripts = Array.from(temp.querySelectorAll('script'));

    scripts.forEach(s => s.remove()); // eliminar del HTML

    // 3. Insertar HTML restante
    divContent.innerHTML = temp.innerHTML;

    // 4. Crear e insertar scripts dinámicamente
    scripts.forEach(oldScript => {
      const newScript = document.createElement('script');

      if (oldScript.src) {
        newScript.src = oldScript.src;
      } else {
        newScript.textContent = oldScript.textContent;
      }

      divContent.appendChild(newScript);
    });

  }
  /**
   * Handler of Button
   * @param {Event} ev 
   */
  static handlerButtonEvent(ev) {
    let name = ev.target.getAttribute('ats-name'),
      view = ev.target.getAttribute('ats-parent'),
      prior = window[view].current;

    window[view].__selectContent(ev.target);

    let current = window[view].current;
    // onClick Event
    if (typeof window[view].onClick == "function") window[view].onClick({ name });

    // onChange Event
    if (prior != current && typeof window[view].onChange == "function") window[view].onChange({ prior, current });

    ev.stopPropagation();
  }

  /**
   * Handler of Close
   * @param {Event} ev 
   */
  static handlerCloseEvent(ev) {
    ev.stopPropagation();
    let view = ev.target.getAttribute("ats-parent"),
      name = ev.target.getAttribute("ats-name"),
      prior = window[view].current;

    // Module onClose Event
    if (window[name] == null) console.error(`${name} no fue creado.`);
    let proceed = (typeof window[name].onClose == "function")
      ? window[name].onClose() : true;

    if (typeof proceed !== "boolean") proceed = true;

    if (proceed === false) return;

    document.getElementById(`${view}-${name}-button`)
      .remove();

    document.getElementById(`${view}-${name}`)
      .remove();

    let i = window[view].contents.findIndex(val => val == name);

    window[view].contents.splice(i, 1);

    if (window[view].contents.length == 0) return;

    window[view].__selectContent(window[view][window[view].contents[--i]].button);

    // onChange Event
    if (prior != window[view].current && typeof window[view].onChange == "function")
      window[view].onChange({ prior, current: window[view].current });
  }
};

/**
 * TabGroupContent
 */
class TabGroupContent {
  /**
   * 
   * @param {String} name 
   * @param {Module} owner 
   * @param {TabGroup} parent 
   * @param {DIV} button 
   */
  constructor(name, owner, parent, button) {
    this.name = name;
    this.owner = owner;
    this.parent = parent;
    this.button = button;
    this.element;

    this.init();
  }

  init() {
    this.element = document.getElementById(`${this.parent.name}-${this.name}`);

    if (!this.element) throw new Error(`${this.name} no existe.`);
  }

  show() {
    this.button.classList.add("active");
    this.element.classList.add("show");
  }

  hide() {
    this.button.classList.remove("active");
    this.element.classList.remove("show");
  }

  update(content) {
    this.element.innerHTML = content;
  }

  fitToParentHeight(margin = 0) {
    if (this.element.offsetParent == null) return;
    // offset parent es the nearest ancestor, if not exists, then offset parent is
    // document body. The nearest ancestor of all Components is "content" block.
    // w3schools.com/jsref

    let h = this.element.offsetParent.offsetHeight - (this.element.offsetTop + margin);
    this.element.style.height = `${h}px`;
  }

  fitToParentWidth() {
    if (this.element.offsetParent == null) return;
    // offset parent es the nearest ancestor, if not exists, then offset parent is
    // document body. The nearest ancestor of all Components is "content" block.
    // w3schools.com/jsref

    let w = this.element.offsetParent.offsetWidth - (this.element.offsetLeft + 10);
    this.element.style.width = `${w}px`;
  }
};

/**
 * Panel
 */
class Panel extends Component {

  constructor(name, owner) {
    super(name, owner);

    this.panels = new Map();
  }

  init() {
    super.init();

    this.panels.set("left", this.element.querySelector('.panel.left'));
    this.panels.set("content", this.element.querySelector('.panel.content'));
    this.panels.set("right", this.element.querySelector('.panel.right'));
  }

  /**
   * Open Module
   * @param {String} path 
   * @param {Object} data 
   */
  openModule(path, data) {
    if (typeof data == "undefined") data = {};

    this.owner.send(path, {
      data,
      onDone: module => {
        // Limpia el contenido
        const panel = this.panels.get('content');
        while (panel.hasChildNodes()) panel.removeChild(panel.firstChild);

        // No existe
        var content = document.createElement("DIV");
        content.id = `${this.name}-${module.name}`;

        this.__parseScripts(content, module.content);

        panel.append(content);

        // Module onOpen Event
        if (window[module.name] == null) console.error(`${module.name} no fue creado.`);
        ats.attachModule(window[module.name]);
      },
      onFail: rs => console.error(rs)
    });
  }

  /**
   * Parse Scripts
   * Generado por ChatGPT
   */
  __parseScripts(divContent, htmlString) {
    // 1. Crear contenedor temporal
    const temp = document.createElement('div');
    temp.innerHTML = htmlString;

    // 2. Extraer scripts
    const scripts = Array.from(temp.querySelectorAll('script'));

    scripts.forEach(s => s.remove()); // eliminar del HTML

    // 3. Insertar HTML restante
    divContent.innerHTML = temp.innerHTML;

    // 4. Crear e insertar scripts dinámicamente
    scripts.forEach(oldScript => {
      const newScript = document.createElement('script');

      if (oldScript.src) {
        newScript.src = oldScript.src;
      } else {
        newScript.textContent = oldScript.textContent;
      }

      divContent.appendChild(newScript);
    });

  }
};

