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
    .then(response => dispatch({ type: FRONTPAGE_FETCH_FINISHED, data: response }))
    .catch(e => {
      console.error(e);

      dispatch({ type: FRONTPAGE_FETCH_FAILED })
    });
};

export const updateFrontpage = (delta, value) => dispatch => {
  dispatch({ type: FRONTPAGE_DELTA_CHANGE_STARTED, delta, value });

  updateFrontpageRest(delta, value)
    .then(response => response.json())
    .then(response => dispatch({
      type: FRONTPAGE_DELTA_CHANGE_FINISHED,
      data: response.data,
      delta: response.delta,
      value: response.value,
    }))
    .catch(e => {
      console.error('here', e);

      dispatch({ type: FRONTPAGE_DELTA_CHANGE_FAILED })
    });
};