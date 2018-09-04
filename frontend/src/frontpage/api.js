export const fetchFrontPageRest = () => fetch('/rest/frontpage');

export const updateFrontpageRest = type => fetch('/rest/frontpage', {
  method: 'put',
  credentials: 'same-origin',
  body: JSON.stringify({
    type
  })
});