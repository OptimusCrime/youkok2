import {
  ADMIN_DIAGNOSTICS_CACHE_FETCH_STARTED,
  ADMIN_DIAGNOSTICS_CACHE_FETCH_FINISHED,
  ADMIN_DIAGNOSTICS_CACHE_FETCH_FAILED,
} from "./constants";

const defaultState = {
  started: false,
  finished: false,
  failed: false,

  data: []
};

export const graph = (state = defaultState, action) => {
  switch (action.type) {

    case ADMIN_DIAGNOSTICS_CACHE_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case ADMIN_DIAGNOSTICS_CACHE_FETCH_FINISHED:
      return {
        ...state,
        data: action.data,
        finished: true,
        started: false,
      };

    case ADMIN_DIAGNOSTICS_CACHE_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
