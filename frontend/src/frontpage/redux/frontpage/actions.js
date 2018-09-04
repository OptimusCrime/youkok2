import {fetchFrontPageRest, updateFrontpageRest} from '../../api';
import {
  FRONTPAGE_FETCH_FAILED,
  FRONTPAGE_FETCH_FINISHED,
  FRONTPAGE_FETCH_STARTED,

  FRONTPAGE_RESET_STARTED
} from './constants';

export const fetchFrontpage = () => dispatch => {
  dispatch({ type: FRONTPAGE_FETCH_STARTED });

  fetchFrontPageRest()
    .then(response => response.json())
    .then(data => dispatch({ type: FRONTPAGE_FETCH_FINISHED, data: data }))
    .catch(dispatch({ type: FRONTPAGE_FETCH_FAILED }));
};

export const updateFrontpage = type => dispatch => {
  dispatch({ type: FRONTPAGE_RESET_STARTED, frontpageType: type });

  updateFrontpageRest(type);
};