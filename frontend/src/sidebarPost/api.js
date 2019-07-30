import 'abortcontroller-polyfill/dist/abortcontroller-polyfill-only'
import {fetch} from 'whatwg-fetch'

const controllers = [];

const abortableFetch = ('signal' in new Request('')) ? window.fetch : fetch;

export const fetchTitleFromUrlRest = url => {
  try {
    controllers.forEach(controller => controller.abort());
    controllers.length = 0;
  }
  catch (ex) {
    //
  }

  const controller = new AbortController();
  const signal = controller.signal;

  controllers.push(controller);

  return abortableFetch('/rest/sidebar/post/title', {
    body: JSON.stringify({
      url
    }),
    method: 'PUT',
    signal: signal
  })
};

export const postLinkRest = (url, title) =>  fetch('/rest/sidebar/post/create/link', {
    body: JSON.stringify({
      id: window.SITE_DATA.archive_id,
      url,
      title,
    }),
    method: 'PUT',
  });

export const uploadFileRest = file =>  {
  const formData = new FormData();
  formData.append('file', file, file.name);

  return fetch(`/rest/sidebar/post/create/file/${window.SITE_DATA.archive_id}`, {
    method: 'POST',
    body: formData,
  });
};
