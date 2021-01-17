import {
  SIDEBAR_HISTORY_FETCH_STARTED,
  SIDEBAR_HISTORY_FETCH_FINISHED,
  SIDEBAR_HISTORY_FETCH_FAILED,
} from "./constants";

const defaultState = {
  started: false,
  finished: false,
  failed: false,

  data: []
};

export const history = (state = defaultState, action) => {
  switch (action.type) {

    case SIDEBAR_HISTORY_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case SIDEBAR_HISTORY_FETCH_FINISHED:
      return {
        ...state,
        data: action.data,
        finished: true,
        started: false,
      };

    case SIDEBAR_HISTORY_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
