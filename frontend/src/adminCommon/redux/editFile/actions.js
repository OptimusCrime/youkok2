import {
  fetchAdminFileDetailsRest,
  putAdminEditFileRest,
} from '../../api';
import {
  ADMIN_EDIT_FILE_PUT_STARTED,
  ADMIN_EDIT_FILE_PUT_FINISHED,
  ADMIN_EDIT_FILE_PUT_FAILED,
  ADMIN_EDIT_FILE_SHOW_MODAL,
  ADMIN_EDIT_FILE_HIDE_MODAL,
  ADMIN_EDIT_FILE_FETCH_STARTED,
  ADMIN_EDIT_FILE_FETCH_FINISHED,
  ADMIN_EDIT_FILE_FETCH_FAILED,
} from './constants';
import {mapUpdateFile} from "./mappers";

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

export const putAdminEditFile = (file, course, data, closeModal) => dispatch => {
  dispatch({ type: ADMIN_EDIT_FILE_PUT_STARTED, course });

  if (closeModal) {
    dispatch({ type: ADMIN_EDIT_FILE_HIDE_MODAL });
  }

  putAdminEditFileRest(file, mapUpdateFile(course, data))
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_EDIT_FILE_PUT_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);
      alert('Her gikk visst noe galt!');
      dispatch({ type: ADMIN_EDIT_FILE_PUT_FAILED })
    });
};
