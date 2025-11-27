
      (function(){
        // util cookie mejorada
        function setCookie(name, value, days){
          var d = new Date();
          d.setTime(d.getTime() + (days*24*60*60*1000));
          var cookie = name + '=' + encodeURIComponent(value) + '; expires=' + d.toUTCString() + '; path=/';
          // si estamos en https podemos añadir Secure
          if (location.protocol === 'https:') cookie += '; Secure';
          // añadir SameSite (opcional)
          cookie += '; SameSite=Lax';
          document.cookie = cookie;
          // debug
          console.log('Cookie set:', cookie);
        }
        function getCookie(name){
          var m = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
          return m ? decodeURIComponent(m[2]) : '';
        }
        function applyColor(color){
          var nav = document.querySelector('nav.navbar');
          var ft = document.querySelector('footer');
          if(nav) nav.style.background = color;
          if(ft) ft.style.background = color;
          // también guardar en variable CSS para estilos si lo usas
          document.documentElement.style.setProperty('--site-theme-color', color);
        }

        // Mostrar modal siempre al cargar
        function showModal(){ var md = document.getElementById('themeModal'); if(!md) return; md.style.display='flex'; md.setAttribute('aria-hidden','false'); md.querySelector('.preset-color')?.focus(); }
        function hideModal(){ var md = document.getElementById('themeModal'); if(!md) return; md.style.display='none'; md.setAttribute('aria-hidden','true'); }

        document.addEventListener('DOMContentLoaded', function(){
          try {
            var saved = getCookie('site_theme_color');
            if (saved) applyColor(saved);

            // siempre mostrar el popup al entrar
            showModal();

            // handlers
            document.querySelectorAll('.preset-color').forEach(function(b){
              b.addEventListener('click', function(){ document.getElementById('customColor').value = this.getAttribute('data-color'); });
            });
            document.getElementById('applyTheme').addEventListener('click', function(){
              var col = document.getElementById('customColor').value || '#ff7a18';
              setCookie('site_theme_color', col, 30); // guardar 30 días
              applyColor(col);
              // notificar al resto de la página (header escucha este evento)
              document.dispatchEvent(new CustomEvent('themeChanged', { detail: { color: col } }));
              hideModal();
            });
            document.getElementById('closeTheme').addEventListener('click', function(){
              hideModal();
            });
          } catch(e){
            console.error(e);
          }
        });
      })();
 

    

      (function(){
        var COOKIE_NAME = 'cookies_accepted';
        function getCookie(name){
          var m = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
          return m ? decodeURIComponent(m[2]) : '';
        }
        function setCookie(name, value, days){
          var expires = '';
          if (days){
            var d = new Date();
            d.setTime(d.getTime() + (days*24*60*60*1000));
            expires = '; expires=' + d.toUTCString();
          }
          document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
        }
        function showModal(){
          var bd = document.getElementById('cookieBackdrop');
          if(!bd) return;
          bd.style.display = 'flex';
          bd.setAttribute('aria-hidden','false');
          // trap focus on first button for accessibility
          var btn = document.getElementById('acceptCookies');
          if(btn) btn.focus();
        }
        function hideModal(){
          var bd = document.getElementById('cookieBackdrop');
          if(!bd) return;
          bd.style.display = 'none';
          bd.setAttribute('aria-hidden','true');
        }
        function showBlocker(){
          var blk = document.getElementById('cookieBlocker');
          if(!blk) return;
          blk.style.display = 'flex';
          blk.setAttribute('aria-hidden','false');
          // evitar interacción con el resto
          document.documentElement.style.overflow = 'hidden';
          document.body.style.pointerEvents = 'none';
          blk.style.pointerEvents = 'auto';
        }

        document.addEventListener('DOMContentLoaded', function(){
          try {
            var accepted = getCookie(COOKIE_NAME);
            if (accepted === 'true') {
              return; // no mostrar
            }
            // Mostrar popup
            showModal();

            var btnAccept = document.getElementById('acceptCookies');
            var btnReject = document.getElementById('rejectCookies');

            if (btnAccept) btnAccept.addEventListener('click', function(){
              setCookie(COOKIE_NAME, 'true', 1); // 1 día
              hideModal();
            });

            if (btnReject) btnReject.addEventListener('click', function(){
              hideModal();
              showBlocker();
            });

            // cerrar con Esc (aceptar implícito no, solo cerrar si ya aceptado)
            document.addEventListener('keydown', function(e){
              if(e.key === 'Escape'){
                // si usuario pulsa ESC sin aceptar, tratamos como rechazo
                var acceptedNow = getCookie(COOKIE_NAME);
                if(acceptedNow !== 'true'){
                  hideModal();
                  showBlocker();
                }
              }
            });
          } catch (err){
            // en caso de error mostrar modal por seguridad
            showModal();
          }
        });
      })();
          
    // pequeña mejora: el botón del blocker permite aceptar sin recargar (coincide con la cookie usada por coockie.js)
    document.addEventListener('DOMContentLoaded', function(){
      var btn = document.getElementById('acceptCookiesBlocker');
      if (!btn) return;
      btn.addEventListener('click', function(){
        var d = new Date(); d.setTime(d.getTime() + (1*24*60*60*1000)); // 1 día
        document.cookie = 'cookies_accepted=true; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
        var blk = document.getElementById('cookieBlocker');
        if (blk) { blk.style.display = 'none'; blk.setAttribute('aria-hidden','true'); }
        // restaurar interacción
        document.documentElement.style.overflow = '';
        document.body.style.pointerEvents = '';
        // notificar al script si quiere reaccionar
        document.dispatchEvent(new CustomEvent('cookiesAccepted'));
      });
    });
    
