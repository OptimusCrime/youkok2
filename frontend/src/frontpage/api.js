export const fetchFrontPageBoxesRest = () => fetch('/rest/frontpage/boxes');
export const fetchFrontPagePopularElementsRest = delta => fetch(`/rest/frontpage/popular/elements?delta=${delta}`);
export const fetchFrontPagePopularCoursesRest = delta => fetch(`/rest/frontpage/popular/courses?delta=${delta}`);
export const fetchFrontPageLastDownloadedRest = () => fetch('/rest/frontpage/last/downloaded');
export const fetchFrontPageLastVisitedRest = () => fetch('/rest/frontpage/last/visited');
export const fetchFrontPageNewestRest = () => fetch('/rest/frontpage/newest');
