/**
 * 
 */
(function (window) {
  var
    /**
     * Parse Route
     * @param {string} route
     * @returns {string}
     */
    parseRoute = function(route){
      let patt = /^(\/)/;

      return (patt.test(route)) ? route.replace(patt,'') : route;
    },
    /**
     * openModule
     * @param {string} route
     * @param {object} params
     * @returns {void}
     */
    openModule = function (route, params) {
      route = parseRoute(route);

      if (!params) window.location.replace(BASE_URL + route);

      if (params) {
        // Obtiene el formulario de app
        const form = document.getElementById("appForm");
        if (!form) throw new Error('El formulario de app no existe.');
        form.setAttribute("action", BASE_URL + route);

        // integra parametros
        for (let prm in params) {
          let input = document.createElement("INPUT");
          input.setAttribute("type", "hidden");
          input.setAttribute("name", prm);
          input.setAttribute("value", params[prm]);
          form.appendChild(input);
        }
        // envia formulario
        form.requestSubmit();
      }
    },
    /**
     * attachModule
     * @param {Module} module
     * @returns {void}
     */
    attachModule = function (module) {
      // Mueve cada uno de los componentes "modal" al final
      // de "body".
      const modals = document.querySelectorAll('content .ats-modal');
      const body = document.querySelector('body');
      if (modals && body)
        modals.forEach(modal => {
          // valida que el modal no esté ya en body
          if (!body.contains(modal)) body.appendChild(modal)
        });

      // Inicializa los componentes de Module
      module.initComponents();

      module.onOpen();
    },
    /**
     * Attaches the application form to the body.
     * @returns {void}
     */
    attachForm = function () {
      const body = document.getElementsByTagName("body");

      const form = document.createElement("FORM");
      form.setAttribute("action", "/root");
      form.setAttribute("method", "POST");
      form.setAttribute("id", "appForm");
      form.addEventListener("submit", onSubmit);
      body[0].appendChild(form);
    },
    /**
     * Handles the submission of the application form.
     * @param {Event} event
     * @returns {void}
     */
    onSubmit = function (event) {
      console.log('Enviando appForm');
    },
    /**
     * Adds an event listener to the window.
     * @param {string} eventName
     * @param {Function} callBack
     * @returns {void}
     */
    addEvent = function (eventName, callBack) {
      (window.addEventListener)
        ? window.addEventListener(eventName, callBack)
        : window.attachEvent("on" + eventName, callBack);
    },
    /**
     * Hides the navigation dropdown.
     * @returns {void}
     */
    hideNavDropdown = function () {
      var x = document.querySelectorAll(".ats-menubar .dropdown");

      if (x != null) {
        var i = 0, n = x.length;
        for (; i < n; i++) {
          x[i].classList.remove("show");

          let c = x[i].querySelector(".caret");

          if (c != null) {
            c.classList.add("fa-caret-down");
            c.classList.remove("fa-caret-right");
          }
        }
      }

      // // remueve la modalidad Responsive
      // var navbars = document.getElementsByClassName("ats-navbar");
      // if (navbars != null) {
      //   for (let i = 0; i < navbars.length; i++)
      //     navbars[i].classList.remove("responsive");
      // }
      // var navresponsiveicons = document.getElementsByClassName("ats-nav-responsive-icon");
      // if (navresponsiveicons != null) {
      //   for (let i = 0; i < navresponsiveicons.length; i++)
      //     navresponsiveicons[i].classList.remove("transform");
      // }
    },
    /**
     * Hides the menu options dropdown.
     * @returns {void}
     */
    hideMenuOptionsDropDown = function () {
      const x = document.querySelectorAll("table>tbody>tr>td.menu-options div.content");

      if (x == null) return;

      for (let i = 0; i < x.length; i++) x[i].classList.remove("show");
    },
    /**
     * Starts the loader.
     * @returns {void}
     */
    startLoader = function () {
      document.getElementById("ats-loader").style.display = "block";
    },
    /**
     * Stops the loader.
     * @returns {void}
     */
    stopLoader = function () {
      document.getElementById("ats-loader").style.display = "none";
    },
    info = (message) => { },
    
    /**
     * Send
     * Sends a request to the server.
     * @param {string} route
     * @param {object} options
     * 
     * @returns {void}
     */
    send = function (route, options) {

      var fd = new FormData();
      if (typeof options.data == "object") {
        for (const key in options.data) {
          if (!Object.hasOwn(options.data, key)) continue;

          fd.append(key, options.data[key]);
        }
      }
      // v3.0.20
      if (typeof options.onDone === "function") {
        console.warn('El uso de onDone es obsoleto, use done.');
        options.done = options.onDone;
      }
        
      if (typeof options.done == "undefined") {
        console.error('Debe definir done como función.');
        throw new Error('Debe definir done como función.');
      }

      if (typeof options.onFail === "function") {
        console.warn('El uso de onFail es obsoleto, use fail.');
        options.fail = options.onFail;
      }
      // Si el Usuario no define una acción para onFail, se establece
      // esta acción predeterminada
      if (typeof options.fail === "undefined") {
        options.fail = rs => {
          alert(rs.message);
          console.warn(rs.detail);
        };
      }
      // Si se ha agregado un token CSRF, entonces integra el encabezado.
      var headers = (fd.has('csrf_token')) ? { 'X-CSRF-TOKEN': fd.get('csrf_token') } : {};
      
      // manda analizar la ruta para quitar separador "/" al inicio
      route = parseRoute(route);

      startLoader();
      
      // Note: Se reemplaza $.ajax por ats.post para unificar la forma de enviar solicitudes al servidor.
      post({
        url: BASE_URL + route,
        headers,
        data: fd
      }, {
        done: options.done,
        fail: options.fail
      });
    },
    /**
     * Posts data to the server.
     * @invokedBy atusan.send
     * @invokedBy components.DataForm.handlerFormSubmitEvent
     * @invokedBy controllers.ModuleBase.send
     * 
     * @param {object} options
     * @param {object} callbacks
     * 
     * Object options:
     * - url: string
     * - headers: object
     * - data: FormData
     * Object callbacks:
     * - done: function
     * - fail: function
     * 
     * @returns {void}
     */
    post = function (options, callbacks) {
      options.method = 'POST';
      options.contentType = false;
      options.processData = false;
      options.dataType = 'json'; // ChatGPT / Atusan 3/AJAX - JSON

      $.ajax(options)
        .done(rs => {
          stopLoader();
          // La respuesta obtenida tendrá la estructura:
          // {status, data, message, detail}
          // Se agregó al método json de la clase "Response" la línea
          // `header('Content-Type: application/json');` para que el navegador
          // reconozca la respuesta como JSON y no como texto plano.
          // ChatGPT / Atusan 3/AJAX - JSON
          if (rs.status == 'ok') {
            callbacks.done(rs);
          } else {
            callbacks.fail(rs);
          }
        })
        // Si el Servidor responde con códigos 400s o 500s, se envia a
        // la consola de Errores
        .fail((xhr, status, error) => {
          stopLoader();
          console.error(`AJAX: ${status} - ${error}`);
          console.error(xhr.responseText);
        });
    };

  addEvent("click", hideNavDropdown);
  addEvent("click", hideMenuOptionsDropDown);
  addEvent("unload", stopLoader);

  window.ats = {
    parseRoute,
    openModule,
    attachModule,
    attachForm,
    hideNavDropdown,
    hideMenuOptionsDropDown,
    startLoader,
    stopLoader,
    info,
    send,
    post
  };

  $("document").ready(() => {
    ats.info('Atusan listo!');

    ats.attachForm();
  });
})(window);
