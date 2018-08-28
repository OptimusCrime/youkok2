import { fetchFrontPageRest } from '../../api';
import {
  FRONTPAGE_FETCH_FAILED,
  FRONTPAGE_FETCH_FINISHED,
  FRONTPAGE_FETCH_STARTED
} from './constants';

export const fetchFrontpage = () => dispatch => {
  dispatch({ type: FRONTPAGE_FETCH_STARTED });

  fetchFrontPageRest()
    .then(response => response.json())
    .then(data => dispatch({ type: FRONTPAGE_FETCH_FINISHED, data: data }))
    .catch(dispatch({ type: FRONTPAGE_FETCH_FAILED }));
};