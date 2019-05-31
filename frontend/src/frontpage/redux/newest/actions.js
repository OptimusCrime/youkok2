import {fetchFrontPageNewestRest} from '../../api';
import {
  FRONTPAGE_NEWEST_FETCH_FAILED,
  FRONTPAGE_NEWEST_FETCH_FINISHED,
  FRONTPAGE_NEWEST_FETCH_STARTED,
} from './constants';

export const fetchFrontPageNewest = () => dispatch => {
  dispatch({ type: FRONTPAGE_NEWEST_FETCH_STARTED });

  fetchFrontPageNewestRest()
    .then(response => response.json())
    .then(response => dispatch({ type: FRONTPAGE_NEWEST_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);

      dispatch({ type: FRONTPAGE_NEWEST_FETCH_FAILED })
    });
};

