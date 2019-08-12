import {
  ADMIN_EDIT_FILE_POST_STARTED,
  ADMIN_EDIT_FILE_POST_FINISHED,
  ADMIN_EDIT_FILE_POST_FAILED,
  ADMIN_EDIT_FILE_SHOW_MODAL,
  ADMIN_EDIT_FILE_HIDE_MODAL,
  ADMIN_EDIT_FILE_FETCH_STARTED,
  ADMIN_EDIT_FILE_FETCH_FAILED,
  ADMIN_EDIT_FILE_FETCH_FINISHED,
} from "./constants";

const defaultState = {
  fetchStarted: false,
  fetchFinished: false,
  fetchFailed: false,
  postStarted: false,
  postFinished: false,
  postFailed: false,
  showModal: false,
  courseId: null,
  fileId: null,
  data: {}
};

export const editFile = (state = defaultState, action) => {
  switch (action.type) {

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

    case ADMIN_EDIT_FILE_FETCH_STARTED:
      return {
        ...state,
        fetchStarted: true,
      };

    case ADMIN_EDIT_FILE_FETCH_FINISHED:
      return {
        ...state,
        fetchStarted: false,
        fetchFinished: true,
        data: action.data,
      };

    case ADMIN_EDIT_FILE_FETCH_FAILED:
      return {
        ...state,
        showModal: false,
        fetchStarted: false,
        fetchFailed: true,
      };

    case ADMIN_EDIT_FILE_POST_STARTED:
      return {
        ...state,
        value: '',
        postStarted: true,
        showModal: false,
      };

    case ADMIN_EDIT_FILE_POST_FINISHED:
      return {
        ...state,
        postFinished: true,
        postStarted: false,
      };

    case ADMIN_EDIT_FILE_POST_FAILED:
      return {
        ...state,
        postFailed: true,
        postStarted: false,
      };

    default:
      return state
  }
};
