import {
  ADMIN_EDIT_FILE_POST_STARTED,
  ADMIN_EDIT_FILE_POST_FINISHED,
  ADMIN_EDIT_FILE_POST_FAILED,
  ADMIN_EDIT_FILE_SHOW_MODAL, ADMIN_EDIT_FILE_HIDE_MODAL,
} from "./constants";

const defaultState = {
  started: false,
  finished: false,
  failed: false,
  showModal: false,
  courseId: null,
  fileId: null,
};

export const editFile = (state = defaultState, action) => {
  switch (action.type) {

    case ADMIN_EDIT_FILE_POST_STARTED:
      return {
        ...state,
        value: '',
        started: true,
        showModal: false,
      };

    case ADMIN_EDIT_FILE_POST_FINISHED:
      return {
        ...state,
        finished: true,
        started: false,
      };

    case ADMIN_EDIT_FILE_POST_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    case ADMIN_EDIT_FILE_SHOW_MODAL:
      return {
        ...state,
        showModal: true,
        courseId: action.courseId,
        fileId: action.fileId,
      };

    case ADMIN_EDIT_FILE_HIDE_MODAL:
      return {
        ...state,
        showModal: false,
      };

    default:
      return state
  }
};
