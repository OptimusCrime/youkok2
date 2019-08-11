import {
  ADMIN_CREATE_DIRECTORY_POST_STARTED,
  ADMIN_CREATE_DIRECTORY_POST_FINISHED,
  ADMIN_CREATE_DIRECTORY_POST_FAILED,
  ADMIN_CREATE_DIRECTORY_HIDE_MODAL,
  ADMIN_CREATE_DIRECTORY_SHOW_MODAL, ADMIN_CREATE_DIRECTORY_UPDATE_VALUE,
} from "./constants";

const defaultState = {
  started: false,
  finished: false,
  failed: false,
  showModal: false,
  directoryId: null,
  courseId: null,
  title: '',
  value: '',
};

export const createDirectory = (state = defaultState, action) => {
  switch (action.type) {

    case ADMIN_CREATE_DIRECTORY_POST_STARTED:
      return {
        ...state,
        value: '',
        started: true,
        showModal: false,
      };

    case ADMIN_CREATE_DIRECTORY_POST_FINISHED:
      return {
        ...state,
        finished: true,
        started: false,
      };

    case ADMIN_CREATE_DIRECTORY_POST_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    case ADMIN_CREATE_DIRECTORY_SHOW_MODAL:
      return {
        ...state,
        showModal: true,
        directoryId: action.id,
        courseId: action.course,
        title: action.title,
      };

    case ADMIN_CREATE_DIRECTORY_HIDE_MODAL:
      return {
        ...state,
        showModal: false,
      };

    case ADMIN_CREATE_DIRECTORY_UPDATE_VALUE:
      return {
        ...state,
        value: action.value,
      };

    default:
      return state
  }
};
