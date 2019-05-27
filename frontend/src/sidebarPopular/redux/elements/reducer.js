import {
  SIDEBAR_MOST_POPULAR_FETCH_STARTED,
  SIDEBAR_MOST_POPULAR_FETCH_FINISHED,
  SIDEBAR_MOST_POPULAR_FETCH_FAILED,
} from "./constants";

const defaultState = {
  started: false,
  finished: false,
  failed: false,

  data: []
};

export const elements = (state = defaultState, action) => {
  switch (action.type) {

    case SIDEBAR_MOST_POPULAR_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case SIDEBAR_MOST_POPULAR_FETCH_FINISHED:
      return {
        ...state,
        data: action.data,
        finished: true,
        started: false,
      };

    case SIDEBAR_MOST_POPULAR_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
