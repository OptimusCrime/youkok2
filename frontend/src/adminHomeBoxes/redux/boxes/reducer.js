import {
  ADMIN_HOME_BOXES_FETCH_STARTED,
  ADMIN_HOME_BOXES_FETCH_FINISHED,
  ADMIN_HOME_BOXES_FETCH_FAILED,
} from "./constants";

const defaultState = {
  started: false,
  finished: false,
  failed: false,

  data: []
};

export const boxes = (state = defaultState, action) => {
  switch (action.type) {

    case ADMIN_HOME_BOXES_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case ADMIN_HOME_BOXES_FETCH_FINISHED:
      return {
        ...state,
        data: action.data,
        finished: true,
        started: false,
      };

    case ADMIN_HOME_BOXES_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
