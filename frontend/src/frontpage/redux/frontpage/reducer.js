import {
  mapFrontpage,
  mapFrontpageInfo
} from './mappers';
import {
  FRONTPAGE_FETCH_FAILED,
  FRONTPAGE_FETCH_FINISHED,
  FRONTPAGE_FETCH_STARTED,
} from './constants';
import { DELTA_MOST_POPULAR_MONTH } from "../../consts";

const defaultState = {
  started: false,
  finished: false,
  failed: false,

  info: {
    number_files: null,
    number_downloads: null,
    number_courses_with_content: null,
    number_new_elements: null,
  },

  latest_elements: [],
  courses_last_visited: [],
  courses_last_downloaded: [],

  elements_most_popular: [],
  courses_most_popular: [],

  user_preferences: {
    // These have to be strings...
    DELTA_POST_POPULAR_COURSES: DELTA_MOST_POPULAR_MONTH,
    DELTA_POST_POPULAR_ELEMENTS: DELTA_MOST_POPULAR_MONTH,
  },
};

const frontpage = (state = defaultState, action) => {
  switch (action.type) {

    case FRONTPAGE_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case FRONTPAGE_FETCH_FINISHED:
      return {
        ...state,

        info: {
          ...state,
          ...mapFrontpageInfo(action.data)
        },

        user_preferences: {
          ...action.data.user_preferences
        },

        ...mapFrontpage(action.data),

        finished: true,
        started: false,
      };

    case FRONTPAGE_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};

export default frontpage;