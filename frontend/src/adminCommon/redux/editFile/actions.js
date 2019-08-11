import {
  postAdminCreateDirectoryRest,
} from '../../api';
import {
  ADMIN_EDIT_FILE_POST_STARTED,
  ADMIN_EDIT_FILE_POST_FINISHED,
  ADMIN_EDIT_FILE_POST_FAILED,
  ADMIN_EDIT_FILE_SHOW_MODAL,
  ADMIN_EDIT_FILE_HIDE_MODAL,
} from './constants';

// TODO begin fetch here!
export const showEditFileModal = (courseId, fileId) => ({type: ADMIN_EDIT_FILE_SHOW_MODAL, courseId, fileId});
export const hideEditFileModal = () => ({type: ADMIN_EDIT_FILE_HIDE_MODAL});

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
