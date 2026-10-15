/**
 * Patrón IIFE (Immediately Invoked Function Expression): Gemini
 * Garantiza la compatibilidad total con navegadores antiguos.
 * Mantiene tus funciones organizadas y aisladas del ámbito global.
 * Permite seguir escribiendo código limpio y modular a nivel interno.
 */
(function (window) {
  const
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
     * @param {object} params (Options Object)
     * @returns {void}
     */
    openModule = function (route, params) {
      route = parseRoute(route);

      if (typeof params === 'undefined') {
        window.location.replace(BASE_URL + route);
        return;
      };
      
      const form = buildForm(BASE_URL + route);

      // integra parametros
      for (let prm in params) {
        let input = document.createElement("INPUT");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", prm);
        input.setAttribute("value", params[prm]);
        form.appendChild(input);
      }
      // El formulario debe estar integrado al body para poder ejecutar "submit".
      document.getElementsByTagName("body")[0].appendChild(form);

      // envia formulario (soporte para navegadores antiguos)
      return (typeof form.requestSubmit === 'function')
        ? form.requestSubmit() : form.submit();
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
          const existingModal = body.querySelector(`#${modal.id}`);
          if (existingModal) existingModal.remove();

          body.appendChild(modal);
        });

      // Inicializa los componentes de Module
      module.initComponents();

      module.onOpen();
    },
    /**
     * Build Form to send parámeters in POST request.
     * @param {String} url
     * @returns {void}
     */
    buildForm = function (url) {
      const form = document.createElement("FORM");
      form.setAttribute("action", url);
      form.setAttribute("method", "POST");
      form.setAttribute("id", "appForm");

      return form;
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
    startLoader = () => document.getElementById("ats-loader").style.display = "block",
    /**
     * Stops the loader.
     * @returns {void}
     */
    stopLoader = () => document.getElementById("ats-loader").style.display = "none",
    /**
     * Notify
     */
    notify = message => console.info(message),
    
    /**
     * Send
     * Sends a request to the server.
     * @param {string} route
     * @param {object} options {data, done, fail, csrf}
     * 
     * @returns {void}
     * 
     * @invokedBy controllers.ModuleBase.send
     */
    send = function (route, options) {

      var fd = new FormData();
      // Carga los valores de "data" en el FormData
      if (typeof options.data === "object") {
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
      // Si el Usuario no define una acción para fail, se establece
      // esta acción predeterminada
      if (typeof options.fail === "undefined") {
        options.fail = rs => {
          alert(rs.message);
          console.warn(rs.detail);
        };
      }
      // Gemini: Si se ha agregado un token CSRF, entonces integra el encabezado.
      const csrfToken = options?.csrf ?? (fd.has('csrf_token') ? fd.get('csrf_token') : null);

      const headers = {
        ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
      };
      // manda analizar la ruta para quitar separador "/" al inicio
      route = parseRoute(route);
      
      // Gemini: 
      smartAjax({
        url: BASE_URL + route,
        headers,
        data: fd
      }, {
        done: options.done,
        fail: options.fail
      });
    };
    /**
     * Smart Ajax (Gemini)
     * 
     * @param {Object} options 
     * @param {Object} callbacks 
     * @returns 
     */
    const smartAjax = (options, callbacks) => {
      const supportFetch = typeof window.fetch !== 'undefined';
      
      startLoader();
      
      return supportFetch ? postFetch(options, callbacks) : legacyAjax(options, callbacks);
    };
    /**
     * postFetch
     * @invokedBy atusan.send
     * @invokedBy components.DataForm.handlerFormSubmitEvent
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
     * Producida por: Gemini
     */
    const postFetch = async (options, callbacks) => {
      try {
        const headers = { 
          'Accept': 'application/json', 
          'X-Requested-With': 'XMLHttpRequest',
          ...options.header // Opcional, por si pasas headers como el token CSRF
        };

        // Con fetch, si envías un FormData en el 'body', el navegador calcula 
        // automáticamente el Content-Type con su respectivo 'boundary'.
        const response = await fetch(options.url, {
          method: 'POST',
          body: options.data,
          headers
        });

        // Parseamos la respuesta a JSON
        const rs = await response.json();
        
        stopLoader();

        // Si el servidor responde con códigos 400s o 500s
        if (!response.ok) {
          console.error(`Fetch: ${response.status} ${response.statusText}`);
          console.error(rs); // O response.text() dependiendo de lo que devuelva el servidor
          return;
        }

        // Validación lógica del negocio (status 'ok')
        if (rs.status === 'ok') {
          callbacks.done(rs);
        } else {
          callbacks.fail(rs);
        }

      } catch (error) {
        stopLoader();
        console.error('Fetch Error:', error);
      }
    };
    /**
     * Legacy AJAX (Gemini).
     * @param {Object} options 
     * @param {Object} callbacks 
     */
    const legacyAjax = (options, callbacks) => {
      const xhr = new XMLHttpRequest();
      xhr.open('POST', options.url, true);

      // Headers necesarios
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      
      if (options.header) {
        for (const key in options.header) {
          if (options.header.hasOwnProperty(key)) {
            xhr.setRequestHeader(key, options.header[key]);
          }
        }
      }

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          stopLoader();
          try {
            const rs = JSON.parse(xhr.responseText);
            if (xhr.status >= 200 && xhr.status < 300) {
              if (rs.status === 'ok') {
                callbacks.done(rs);
              } else {
                callbacks.fail(rs);
              }
            } else {
              console.error(`AJAX Error: ${xhr.status}`);
              if (callbacks.fail) callbacks.fail(rs);
            }
          } catch (e) {
            stopLoader();
            console.error('Error parsing JSON:', xhr.responseText);
          }
        }
      };

      // El navegador maneja automáticamente el FormData y su boundary
      xhr.send(options.data);
    };

  addEvent("click", hideNavDropdown);
  addEvent("click", hideMenuOptionsDropDown);
  addEvent("unload", stopLoader);
  
  // Librería pública
  window.Atusan = {
    openModule,
    send
  };
  // Librearía interna
  window.ats = {
    parseRoute, // DataForm
    // openModule,
    attachModule, // Application | Panel | TabGroup
    // attachForm,
    hideNavDropdown, // Navbar
    // hideMenuOptionsDropDown,
    // startLoader,
    // stopLoader,
    notify,
    send, // Module
    smartAjax // send | DataForm
  };


  // Gemini: Estandar, moderna y compatible con todos los navegadores
  // (incluyendo Internet Explorer en Windows 7), 100% nativa.
  document.addEventListener('DOMContentLoaded', () => {
    notify('Atusan listo!');

    const app = new Application('app');

    app.execute();
  });
})(window);
