import {
  fetchSidebarMostPopularRest,
} from '../../api';
import {
  SIDEBAR_MOST_POPULAR_FETCH_FAILED,
  SIDEBAR_MOST_POPULAR_FETCH_FINISHED,
  SIDEBAR_MOST_POPULAR_FETCH_STARTED,
} from './constants';

export const fetchSidebarMostPopular = () => dispatch => {
  dispatch({ type: SIDEBAR_MOST_POPULAR_FETCH_STARTED });

  fetchSidebarMostPopularRest()
    .then(response => response.json())
    .then(response => dispatch({ type: SIDEBAR_MOST_POPULAR_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);

      dispatch({ type: SIDEBAR_MOST_POPULAR_FETCH_FAILED })
    });
};