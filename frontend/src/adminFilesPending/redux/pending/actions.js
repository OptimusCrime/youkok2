import {
  fetchAdminFilesPendingRest,
} from '../../api';
import {
  ADMIN_FILES_PENDING_FETCH_STARTED,
  ADMIN_FILES_PENDING_FETCH_FINISHED,
  ADMIN_FILES_PENDING_FETCH_FAILED,
} from './constants';

export const fetchAdminFilesPending = () => dispatch => {
  dispatch({ type: ADMIN_FILES_PENDING_FETCH_STARTED });

  fetchAdminFilesPendingRest()
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_FILES_PENDING_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);
      dispatch({ type: ADMIN_FILES_PENDING_FETCH_FAILED })
    });
};
