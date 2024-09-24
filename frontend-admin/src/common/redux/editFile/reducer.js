import {
  ADMIN_EDIT_FILE_PUT_STARTED,
  ADMIN_EDIT_FILE_PUT_FINISHED,
  ADMIN_EDIT_FILE_PUT_FAILED,
  ADMIN_EDIT_FILE_SHOW_MODAL,
  ADMIN_EDIT_FILE_HIDE_MODAL,
  ADMIN_EDIT_FILE_FETCH_STARTED,
  ADMIN_EDIT_FILE_FETCH_FAILED,
  ADMIN_EDIT_FILE_FETCH_FINISHED,
  ADMIN_EDIT_FILE_TOGGLE_CHECKBOX,
  ADMIN_EDIT_FILE_CHANGE_PARENT,
  ADMIN_EDIT_FILE_CHANGE_VALUE,
} from "./constants";

const defaultState = {
  fetchStarted: false,
  fetchFinished: false,
  fetchFailed: false,
  putStarted: false,
  putFinished: false,
  putFailed: false,
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

    case ADMIN_EDIT_FILE_PUT_STARTED:
      return {
        ...state,
        putStarted: true,
      };

    case ADMIN_EDIT_FILE_PUT_FINISHED:
      return {
        ...state,
        putFinished: true,
        putStarted: false,
        data: action.data.element
      };

    case ADMIN_EDIT_FILE_PUT_FAILED:
      return {
        ...state,
        putFailed: true,
        putStarted: false,
      };

    case ADMIN_EDIT_FILE_TOGGLE_CHECKBOX:
      return {
        ...state,
        data: {
          ...state.data,
          [action.id]: state.data[action.id] !== true
        }
      };

    case ADMIN_EDIT_FILE_CHANGE_PARENT:
      return {
        ...state,
        data: {
          ...state.data,
          parent: action.parent,
        }
      };

    case ADMIN_EDIT_FILE_CHANGE_VALUE:
      return {
        ...state,
        data: {
          ...state.data,
          [action.id]: action.value,
        }
      };

    default:
      return state
  }
};
