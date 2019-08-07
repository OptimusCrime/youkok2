import {
  ADMIN_FILES_PENDING_FETCH_STARTED,
  ADMIN_FILES_PENDING_FETCH_FINISHED,
  ADMIN_FILES_PENDING_FETCH_FAILED,
} from "./constants";
import {mapPendingData} from "./mappers";

const defaultState = {
  started: false,
  finished: false,
  failed: false,

  data: [],
};

export const pending = (state = defaultState, action) => {
  switch (action.type) {

    case ADMIN_FILES_PENDING_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case ADMIN_FILES_PENDING_FETCH_FINISHED:
      return {
        ...state,
        data: mapPendingData(action.data),
        finished: true,
        started: false,
      };

    case ADMIN_FILES_PENDING_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
