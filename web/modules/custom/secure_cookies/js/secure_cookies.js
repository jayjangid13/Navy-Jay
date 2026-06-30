(function () {

  function setCookie(name, value, path) {
    document.cookie =
      name + "=" + value +
      "; Path=" + path +
      "; SameSite=Strict" +
      "; Secure";
  }

  function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
  }

  // Detect admin pages
  const isAdmin = window.location.pathname.startsWith('/admin');
  const cookiePath = isAdmin ? '/admin' : '/';

  // Fix local cookies
  const local = getCookie('localtimestamp');
  if (local) {
    setCookie('localtimestamp', local, cookiePath);
  }

  // Fix server cookies
  const server = getCookie('servertimestamp');
  if (server) {
    setCookie('servertimestamp', server, cookiePath);
  }

  // Fix Tawk UUID cookies
  document.cookie.split(';').forEach(function (c) {
    const parts = c.split('=');
    const name = parts[0].trim();
    const value = parts[1];

    if (name.startsWith('twk_uuid_') && value) {
      setCookie(name, value, cookiePath);
    }
  });

})();


// (function () {

//   function fixCookie(name) {
//     const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
//     if (match) {
//       const value = match[2];

//       document.cookie =
//         name + "=" + value +
//         "; Path=/" +
//         "; SameSite=Strict" +
//         "; Secure";
//     }
//   }

//   // Fix local cookies
//   fixCookie("localtimestamp");
//   fixCookie("servertimestamp");

//   // Fix twk cookies (UUID)
//   const cookies = document.cookie.split(";");
//   cookies.forEach(function (c) {
//     const name = c.split("=")[0].trim();
//     if (name.startsWith("twk_uuid_")) {
//       fixCookie(name);
//     }
//   });

// })();
