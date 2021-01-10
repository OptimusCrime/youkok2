import {
  ADMIN_FILES_FETCH_STARTED,
  ADMIN_FILES_FETCH_FINISHED,
  ADMIN_FILES_FETCH_FAILED,
} from './constants';

export const fetchAdminFiles = rest => dispatch => {
  dispatch({ type: ADMIN_FILES_FETCH_STARTED });

  rest()
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_FILES_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);
      dispatch({ type: ADMIN_FILES_FETCH_FAILED })
    });
};
