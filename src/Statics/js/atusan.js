/**
 * 
 */
(function (window) {
  var
    parseRoute = function(route){
      let patt = /^(\/)/;

      return (patt.test(route)) ? route.replace(patt,'') : route;
    },
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
    attachModule = function (module) {
      // Mueve cada uno de los componentes "modal" al final
      // de "body".
      const modals = document.querySelectorAll('content .ats-modal');
      const body = document.querySelector('body');
      if (modals && body)
        modals.forEach(modal => body.appendChild(modal));

      // Inicializa los componentes de Module
      module.initComponents();

      module.onOpen();
    },
    attachForm = function () {
      const body = document.getElementsByTagName("body");

      const form = document.createElement("FORM");
      form.setAttribute("action", "/root");
      form.setAttribute("method", "POST");
      form.setAttribute("id", "appForm");
      form.addEventListener("submit", onSubmit);
      body[0].appendChild(form);
    },
    onSubmit = function (event) {
      console.log('Enviando appForm');
    },
    addEvent = function (eventName, callBack) {
      (window.addEventListener)
        ? window.addEventListener(eventName, callBack)
        : window.attachEvent("on" + eventName, callBack);
    },
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
    hideMenuOptionsDropDown = function () {
      const x = document.querySelectorAll("table>tbody>tr>td.menu-options div.content");

      if (x == null) return;

      for (let i = 0; i < x.length; i++) x[i].classList.remove("show");
    },
    startLoader = function () {
      document.getElementById("ats-loader").style.display = "block";
    },
    stopLoader = function () {
      document.getElementById("ats-loader").style.display = "none";
    },
    // info = (message) => console.info(message),
    info = (message) => { },
    
    /**
     * Send
     */
    send = function (route, options) {

      var fd = new FormData();
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
      
      route = parseRoute(route);

      startLoader();
      let url = BASE_URL + route;
      $.ajax({
        url,
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
              options.onFail(rs);
            }
          } catch (e) {
            console.error(e.message);
            console.error(rs);
          }
        })
        .fail((xhr, status, error) => console.error(error))
        .always(() => {
          stopLoader();
          info('Transacción terminada');
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
    send
  };

  $("document").ready(() => {
    ats.info('Atusan listo!');

    ats.attachForm();
  });
})(window);
