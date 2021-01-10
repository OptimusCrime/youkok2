import {ARCHIVE_FETCH_FAILED, ARCHIVE_FETCH_FINISHED, ARCHIVE_FETCH_STARTED} from "./constants";

const defaultState = {
  archive: {},
  started: false,
  finished: false,
  failed: false,
};

export const archive = (state = defaultState, action) => {
  switch (action.type) {

    case ARCHIVE_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case ARCHIVE_FETCH_FINISHED:
      return {
        ...state,
        finished: true,
        started: false,
        archive: action.data,
      };

    case ARCHIVE_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
