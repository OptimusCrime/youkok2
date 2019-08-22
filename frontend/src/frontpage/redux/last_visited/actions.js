import {fetchFrontPageLastVisitedRest} from '../../api';
import {
  FRONTPAGE_LAST_VISITED_FETCH_FAILED,
  FRONTPAGE_LAST_VISITED_FETCH_FINISHED,
  FRONTPAGE_LAST_VISITED_FETCH_STARTED,
} from './constants';

export const fetchFrontPageLastVisited = () => dispatch => {
  dispatch({ type: FRONTPAGE_LAST_VISITED_FETCH_STARTED });

  fetchFrontPageLastVisitedRest()
    .then(response => response.json())
    .then(response => dispatch({ type: FRONTPAGE_LAST_VISITED_FETCH_FINISHED, data: response.data }))
    .catch(() => dispatch({ type: FRONTPAGE_LAST_VISITED_FETCH_FAILED }));
};

