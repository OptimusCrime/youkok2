import {
  fetchFrontPageRest,
  updateFrontpageRest
} from '../../api';
import {
  FRONTPAGE_DELTA_CHANGE_FAILED,
  FRONTPAGE_DELTA_CHANGE_FINISHED,
  FRONTPAGE_DELTA_CHANGE_STARTED,

  FRONTPAGE_FETCH_FAILED,
  FRONTPAGE_FETCH_FINISHED,
  FRONTPAGE_FETCH_STARTED,
} from './constants';

export const fetchFrontpage = () => dispatch => {
  dispatch({ type: FRONTPAGE_FETCH_STARTED });

  fetchFrontPageRest()
    .then(response => response.json())
    .then(data => dispatch({ type: FRONTPAGE_FETCH_FINISHED, data: data }))
    .catch(() => dispatch({ type: FRONTPAGE_FETCH_FAILED }));
};

export const updateFrontpage = (delta, value) => dispatch => {
  dispatch({ type: FRONTPAGE_DELTA_CHANGE_STARTED, delta, value });

  updateFrontpageRest(delta, value)
    .then(response => response.json())
    .then(data => dispatch({ type: FRONTPAGE_DELTA_CHANGE_FINISHED, data: data }))
    .catch(() => dispatch({ type: FRONTPAGE_DELTA_CHANGE_FAILED }));
};