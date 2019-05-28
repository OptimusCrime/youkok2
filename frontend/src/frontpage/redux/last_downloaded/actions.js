import {fetchFrontPageBoxesRest} from '../../api';
import {
  FRONTPAGE_BOXES_FETCH_FAILED,
  FRONTPAGE_BOXES_FETCH_FINISHED,
  FRONTPAGE_BOXES_FETCH_STARTED,
} from './constants';

export const fetchFrontPageBoxes = () => dispatch => {
  dispatch({ type: FRONTPAGE_BOXES_FETCH_STARTED });

  fetchFrontPageBoxesRest()
    .then(response => response.json())
    .then(response => dispatch({ type: FRONTPAGE_BOXES_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);

      dispatch({ type: FRONTPAGE_BOXES_FETCH_FAILED })
    });
};

