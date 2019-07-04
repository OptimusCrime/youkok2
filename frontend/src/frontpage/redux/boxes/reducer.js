import {
  mapFrontPageBoxes
} from './mappers';
import {
  FRONTPAGE_BOXES_FETCH_FAILED,
  FRONTPAGE_BOXES_FETCH_FINISHED,
  FRONTPAGE_BOXES_FETCH_STARTED,
} from './constants';

const defaultState = {
  started: false,
  finished: false,
  failed: false,
  number_files: null,
  number_downloads: null,
  number_courses_with_content: null,
  number_new_elements: null,
};

export const boxes = (state = defaultState, action) => {
  switch (action.type) {

    case FRONTPAGE_BOXES_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case FRONTPAGE_BOXES_FETCH_FINISHED:
      return {
        ...state,
        started: false,
        finished: true,
        ...mapFrontPageBoxes(action.data)
      };

    case FRONTPAGE_BOXES_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
