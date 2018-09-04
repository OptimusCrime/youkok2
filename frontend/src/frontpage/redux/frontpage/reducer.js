import {
  mapFrontpage,
  mapFrontpageInfo
} from './mappers';
import {
  FRONTPAGE_FETCH_FAILED,
  FRONTPAGE_FETCH_FINISHED,
  FRONTPAGE_FETCH_STARTED, FRONTPAGE_RESET_STARTED
} from './constants';
import {FRONTPAGE_RESET_HISTORY_TYPE} from "../../consts";

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

  elements_most_popular: [],
  courses_most_popular: [],

  user_preferences: [],
  user_favorites: [],
  user_history: [],
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

    case FRONTPAGE_RESET_STARTED:
      return {
        ...state,
        user_history: action.frontpageType === FRONTPAGE_RESET_HISTORY_TYPE ? [] : state.user_history
      };

    default:
      return state
  }
};

export default frontpage;