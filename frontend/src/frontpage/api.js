export const fetchFrontPageRest = () => fetch('/rest/frontpage');

// TODO
export const updateFrontpageRest = type => fetch('/rest/frontpage', {
  method: 'put',
  credentials: 'same-origin',
  body: JSON.stringify({
    type
  })
});