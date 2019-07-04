export const fetchFrontPageBoxesRest = () => fetch('/rest/frontpage/boxes');
export const fetchFrontPagePopularElementsRest = () => fetch('/rest/frontpage/popular/elements');
export const fetchFrontPagePopularCoursesRest = () => fetch('/rest/frontpage/popular/courses');
export const fetchFrontPageLastDownloadedRest = () => fetch('/rest/frontpage/last/downloaded');
export const fetchFrontPageLastVisitedRest = () => fetch('/rest/frontpage/last/visited');
export const fetchFrontPageNewestRest = () => fetch('/rest/frontpage/newest');

export const updateFrontpageRest = (delta, value) => fetch('/rest/frontpage', {
  method: 'put',
  credentials: 'same-origin',
  body: JSON.stringify({
    delta,
    value
  })
});
