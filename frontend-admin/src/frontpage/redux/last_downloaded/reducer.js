import {
  FRONTPAGE_LAST_DOWNLOADED_FETCH_FAILED,
  FRONTPAGE_LAST_DOWNLOADED_FETCH_FINISHED,
  FRONTPAGE_LAST_DOWNLOADED_FETCH_STARTED,
} from './constants';

const defaultState = {
  started: false,
  finished: false,
  failed: false,
  elements: []
};

export const lastDownloaded = (state = defaultState, action) => {
  switch (action.type) {

    case FRONTPAGE_LAST_DOWNLOADED_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case FRONTPAGE_LAST_DOWNLOADED_FETCH_FINISHED:
      return {
        ...state,
        started: false,
        finished: true,
        elements: action.data
      };

    case FRONTPAGE_LAST_DOWNLOADED_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
