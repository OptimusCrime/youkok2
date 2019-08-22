import {
  fetchAdminHomeBoxesRest,
} from '../../api';
import {
  ADMIN_HOME_BOXES_FETCH_STARTED,
  ADMIN_HOME_BOXES_FETCH_FINISHED,
  ADMIN_HOME_BOXES_FETCH_FAILED,
} from './constants';

export const fetchAdminHomeBoxes = () => dispatch => {
  dispatch({ type: ADMIN_HOME_BOXES_FETCH_STARTED });

  fetchAdminHomeBoxesRest()
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_HOME_BOXES_FETCH_FINISHED, data: response.data }))
    .catch(() => dispatch({ type: ADMIN_HOME_BOXES_FETCH_FAILED }));
};
