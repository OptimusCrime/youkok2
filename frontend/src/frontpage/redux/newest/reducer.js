import {
  FRONTPAGE_NEWEST_FETCH_FAILED,
  FRONTPAGE_NEWEST_FETCH_FINISHED,
  FRONTPAGE_NEWEST_FETCH_STARTED,
} from './constants';

const defaultState = {
  started: false,
  finished: false,
  failed: false,
  elements: []
};

export const newest = (state = defaultState, action) => {
  switch (action.type) {

    case FRONTPAGE_NEWEST_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case FRONTPAGE_NEWEST_FETCH_FINISHED:
      return {
        ...state,
        started: false,
        finished: true,
        elements: action.data
      };

    case FRONTPAGE_NEWEST_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
