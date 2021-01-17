import 'abortcontroller-polyfill/dist/abortcontroller-polyfill-only';
import {fetch} from 'whatwg-fetch';

export const fetchArchiveDataRest = (course, path) => fetch(`/rest/archive/data?course=${course}&path=${encodeURIComponent(path)}`);
export const fetchArchiveContentRest = id => fetch(`/rest/archive/content?id=${id}`);

export const fetchSidebarHistoryRest = id => fetch(`/rest/sidebar/history/${id}`);

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

export const postLinkRest = (id, url, title) =>  fetch('/rest/sidebar/post/create/link', {
  body: JSON.stringify({
    id,
    url,
    title,
  }),
  method: 'PUT',
});

export const uploadFileRest = (id, file) =>  {
  const formData = new FormData();
  formData.append('file', file, file.name);

  return fetch(`/rest/sidebar/post/create/file/${id}`, {
    method: 'POST',
    body: formData,
  });
};
