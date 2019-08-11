import {
  postAdminCreateDirectoryRest,
} from '../../api';
import {
  ADMIN_CREATE_DIRECTORY_POST_STARTED,
  ADMIN_CREATE_DIRECTORY_POST_FINISHED,
  ADMIN_CREATE_DIRECTORY_POST_FAILED,
  ADMIN_CREATE_DIRECTORY_SHOW_MODAL,
  ADMIN_CREATE_DIRECTORY_HIDE_MODAL,
  ADMIN_CREATE_DIRECTORY_UPDATE_VALUE,
} from './constants';

export const showCreateDirectoryModal = (id, course, title) => ({type: ADMIN_CREATE_DIRECTORY_SHOW_MODAL, id, course, title});
export const hideCreateDirectoryModal = () => ({type: ADMIN_CREATE_DIRECTORY_HIDE_MODAL});

export const updateModalValue = value => ({type: ADMIN_CREATE_DIRECTORY_UPDATE_VALUE, value});

export const postAdminCreateDirectory = (directory, course, value) => dispatch => {
  dispatch({ type: ADMIN_CREATE_DIRECTORY_POST_STARTED, course });

  postAdminCreateDirectoryRest(directory, course, value)
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_CREATE_DIRECTORY_POST_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);
      alert('Her gikk visst noe galt!');
      dispatch({ type: ADMIN_CREATE_DIRECTORY_POST_FAILED })
    });
};
