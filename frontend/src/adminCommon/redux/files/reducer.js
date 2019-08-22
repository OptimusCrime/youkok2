import {
  ADMIN_FILES_FETCH_STARTED,
  ADMIN_FILES_FETCH_FINISHED,
  ADMIN_FILES_FETCH_FAILED,
} from "./constants";
import {mapData, mapDisabled, mapUpdated} from "./mappers";
import {ADMIN_CREATE_DIRECTORY_POST_FINISHED, ADMIN_CREATE_DIRECTORY_POST_STARTED} from "../createDirectory/constants";
import {ADMIN_EDIT_FILE_PUT_FINISHED, ADMIN_EDIT_FILE_PUT_STARTED} from "../editFile/constants";

const defaultState = {
  started: false,
  finished: false,
  failed: false,

  data: [],
};

export const files = (state = defaultState, action) => {
  switch (action.type) {

    case ADMIN_FILES_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case ADMIN_FILES_FETCH_FINISHED:
      return {
        ...state,
        data: mapData(action.data),
        finished: true,
        started: false,
      };

    case ADMIN_FILES_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    case ADMIN_CREATE_DIRECTORY_POST_STARTED:
    case ADMIN_EDIT_FILE_PUT_STARTED:
      return {
        ...state,
        data: mapDisabled(action.course, state.data)
      };

    case ADMIN_CREATE_DIRECTORY_POST_FINISHED:
      return {
        ...state,
        data: mapUpdated(action.data, state.data)
      };

    case ADMIN_EDIT_FILE_PUT_FINISHED:
      return {
        ...state,
        data: mapUpdated(action.data.course, state.data)
      };

    default:
      return state
  }
};
