export const adminFetch = (url, init) =>
  fetch(url, init)
    .then(response => {
      if (response.status === 403) {
        window.location.href = document.getElementsByTagName('base')[0].href;
      }

      return new Promise(resolve => {
        return resolve(response);
      })
    });
