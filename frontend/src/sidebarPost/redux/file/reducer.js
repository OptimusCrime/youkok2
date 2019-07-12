import {
  SIDEBAR_POST_FILE_ADD_FILE,
  SIDEBAR_POST_FILE_REMOVE_FILE,
  SIDEBAR_POST_FILE_RESET,
  SIDEBAR_POST_FILE_UPLOADS_STARTED,
  SIDEBAR_POST_FILE_UPLOADS_FINISHED,
  SIDEBAR_POST_FILE_UPLOAD_STARTED,
  SIDEBAR_POST_FILE_UPLOAD_FINISHED, SIDEBAR_POST_FILE_UPLOAD_FAILED,
} from "./constants";
import {mapFile, mapFileUploadFinished, mapFileUploadStarted} from "./mappers";

const defaultState = {
  files: [],
  messages: [],
  upload_started: false,
  upload_finished: false,
};

export const file = (state = defaultState, action) => {
  switch (action.type) {

    case SIDEBAR_POST_FILE_ADD_FILE:
      return {
        ...state,
        files: [...state.files, mapFile(action.file)],
      };

    case SIDEBAR_POST_FILE_REMOVE_FILE:
      // .........
      const newFiles = [...state.files];
      newFiles.splice(action.index, 1);

      return {
        ...state,
        files: newFiles
      };

    case SIDEBAR_POST_FILE_UPLOADS_STARTED:
      return {
        ...state,
        upload_started: true,
        upload_finished: false,
      };

    case SIDEBAR_POST_FILE_UPLOADS_FINISHED:
      return {
        ...state,
        upload_started: false,
        upload_finished: true,
      };

    case SIDEBAR_POST_FILE_UPLOAD_STARTED:
      return {
        ...state,
        files: mapFileUploadStarted(state.files, action.index)
      };

    case SIDEBAR_POST_FILE_UPLOAD_FINISHED:
      return {
        ...state,
        files: mapFileUploadFinished(state.files, action.index)
      };

    case SIDEBAR_POST_FILE_UPLOAD_FAILED:
      return {
        ...state,
        files: mapFileUploadFailed(state.files, action.index)
      };

    case SIDEBAR_POST_FILE_RESET:
      return defaultState;

    default:
      return state
  }
};
