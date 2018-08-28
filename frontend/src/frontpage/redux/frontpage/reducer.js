import {
  mapFrontpage,
  mapFrontpageInfo
} from './mappers';
import {
  FRONTPAGE_FETCH_FAILED,
  FRONTPAGE_FETCH_FINISHED,
  FRONTPAGE_FETCH_STARTED
} from './constants';

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

  elements_new: [],
  courses_last_visited: [],

  elements_most_popular: [],
  courses_most_popular: [],

  user_preferences: [],
  user_favorites: [],
  user_last_visited_courses: [],
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

    default:
      return state
  }
};

export default frontpage;