export const fetchFrontPageRest = () => fetch('/rest/frontpage');

export const updateFrontpageRest = (delta, value) => fetch('/rest/frontpage', {
  method: 'put',
  credentials: 'same-origin',
  body: JSON.stringify({
    delta,
    value
  })
});