/**
 * 
 */
class ControlBase {
  constructor(view, name, input) {
    this.view = view;
    this.name = name;
    this.input = input;
    this.type = this.setType();
  }

  setType() {
    throw new Error(`${this.name} de ${this.view.name} no ha implementado setType.`);
  }


  static instance(controlType, view, name, input) {

    switch (controlType) {
      case 'autocomplete':
        return new ControlAutoComplete(view, name, input);
      case 'file':
        return new ControlFile(view, name, input);
      case 'select':
      case 'select-one':
        return new ControlSelect(view, name, input);
      default:
        return null;
    }
  }
};

/**
 * 
 */
class ControlAutoComplete extends ControlBase {
  constructor(view, name, input) {
    super(view, name, input);

    this.items = [];
    this.current = -1;

    this.divlist = input.nextElementSibling;
  }

  setType() {
    return 'AutoComplete';
  }

  feedList(list) {
    // establece dimension de la lista
    this.divlist.style.width = `${this.input.offsetWidth}px`;
    // limpia opciones
    while (this.divlist.hasChildNodes()) this.divlist.removeChild(this.divlist.firstChild);
    // limpia colección de items
    this.items = [];

    for (let i = 0; i < list.length; i++) this.appendItem(i, list[i]);
    this.current = -1;
  }

  /**
   * Append Item
   * @param {Integer} index 
   * @param {String} text 
   */
  appendItem(index, text) {
    let item = this.buildItem(text);

    this.divlist.appendChild(item);
    this.items[index] = item;
  }

  /**
   * Build Item
   * @param {String} text 
   * @returns DIV
   */
  buildItem(text) {
    const itself = this;
    const divitem = document.createElement("div");
    divitem.classList.add('item');
    let textnode = document.createTextNode(text);
    divitem.appendChild(textnode);
    divitem.appendChild(this.createInput('hidden', 'ac-value', text));

    // Evento onClick sobre el Item de la lista
    divitem.addEventListener("click", function (ev) {
      let value = this.querySelector('.ac-value').value;
      // establece el valor del item en la vista
      itself.view.setItem(itself.name, value);
      // dispara evento onChange
      itself.view.onChange({
        view: itself.view.name,
        control: itself.name,
        value
      });
      // limpia opciones
      while (itself.divlist.hasChildNodes()) itself.divlist.removeChild(itself.divlist.firstChild);
    });

    return divitem;
  }

  createInput(type, cssClass, value) {
    const input = document.createElement("input");
    input.setAttribute("type", type);
    input.classList.add(cssClass);
    input.value = value;

    return input;
  }

  setActive() {
    if (this.items.length == 0 ) return;

    this.unsetActive();

    if (this.current >= this.items.length) this.current = 0;
    if (this.current < 0) this.current = (this.items.length - 1);

    this.items[this.current].classList.add("active");
  }

  unsetActive() {
    let i = 0, nof = this.divlist.children.length;
    for (; i < nof; i++) this.divlist.children.item(i).classList.remove("active");
  }

  processKeyDown(ev) {
    const arrowDown = 40,
      arrowUp = 38,
      enter = 13;

    if (ev.keyCode == arrowDown) {
      this.current++;
      this.setActive();
    } else if (ev.keyCode == arrowUp) {
      this.current--;
      this.setActive();
    } else if (ev.keyCode == enter) {
      ev.preventDefault();
      if (this.current > -1) this.items[this.current].click();
    }
  }
  /**
   * Input .control-autocomplete
   */
  static handlerKeyDown(ev) {
    const keyEnter = 13, keyArrowUp = 38, keyArrowDown = 40;
    let keys = [keyEnter, keyArrowUp, keyArrowDown];

    if (keys.includes(ev.keyCode)) {
      let { view, control } = DataViewBase.getControlId(ev.target);

      let lists = window[view].lists();

      if (!lists.has(control)) throw new Error(`${control} no es una lista en ${view}`);

      if (lists.get(control).type != 'AutoComplete') throw new Error(`${control} no es AutoComplete`);

      lists.get(control).processKeyDown(ev);
    }
  }
};

class ControlFile extends ControlBase {
  constructor(view, name, input) {
    super(view, name, input);

    this.label = view.element.querySelector(`#${view.name}-${name}-label`);
  }

  setType() {
    return 'File';
  }

  /**
   * 
   * @param {FileList} list 
   */
  feedList(list) {
    // visualiza nombre de archivos
    if (list.length == 0)
      this.label.innerText = "Elige un archivo";
    else {
      let content = [];
      for (let i = 0; i < list.length; i++) content.push(list[i].name);

      this.label.innerText = content.join(", ");
    }
  }
};

class ControlSelect extends ControlBase {
  constructor(view, name, input) {
    super(view, name, input);
  }

  setType() {
    return 'Select';
  }

  feedList(list) {
    // limpia las opciones
    while (this.input.options.item(0)) this.input.options.remove(0);

    // alimenta la lista
    for (let i = 0; i < list.length; i++) this.appendOption(list[i]);
  }

  appendOption(element) {
    this.input.options.add(this.buildOption(element.text, element.value));
  }

  buildOption(text, value) {
    let opt = document.createElement("option");
    opt.text = text;
    opt.value = value;

    return opt;
  }
};