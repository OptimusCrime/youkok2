import {
  fetchAdminFileDetailsRest,
  postAdminCreateDirectoryRest,
} from '../../api';
import {
  ADMIN_EDIT_FILE_POST_STARTED,
  ADMIN_EDIT_FILE_POST_FINISHED,
  ADMIN_EDIT_FILE_POST_FAILED,
  ADMIN_EDIT_FILE_SHOW_MODAL,
  ADMIN_EDIT_FILE_HIDE_MODAL,
  ADMIN_EDIT_FILE_FETCH_STARTED,
  ADMIN_EDIT_FILE_FETCH_FINISHED,
  ADMIN_EDIT_FILE_FETCH_FAILED,
} from './constants';

export const hideEditFileModal = () => ({type: ADMIN_EDIT_FILE_HIDE_MODAL});

export const showEditFileModal = (courseId, fileId) => dispatch => {
  dispatch({ type: ADMIN_EDIT_FILE_SHOW_MODAL, courseId, fileId });
  dispatch({ type: ADMIN_EDIT_FILE_FETCH_STARTED });

  fetchAdminFileDetailsRest(fileId)
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_EDIT_FILE_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);
      alert('Her gikk visst noe galt!');
      dispatch({ type: ADMIN_EDIT_FILE_FETCH_FAILED })
    });
};

/*
export const postAdminCreateDirectory = (directory, course, value) => dispatch => {
  dispatch({ type: ADMIN_EDIT_FILE_POST_STARTED, course });

  postAdminCreateDirectoryRest(directory, course, value)
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_EDIT_FILE_POST_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);
      alert('Her gikk visst noe galt!');
      dispatch({ type: ADMIN_EDIT_FILE_POST_FAILED })
    });
};
 */
