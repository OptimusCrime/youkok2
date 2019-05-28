import {
  mapFrontpageBoxes
} from './mappers';
import {
  FRONTPAGE_BOXES_FETCH_FAILED,
  FRONTPAGE_BOXES_FETCH_FINISHED,
  FRONTPAGE_BOXES_FETCH_STARTED,

  FRONTPAGE_DELTA_CHANGE_FAILED,
  FRONTPAGE_DELTA_CHANGE_FINISHED,
  FRONTPAGE_DELTA_CHANGE_STARTED,
} from './constants';
import {
  DELTA_MOST_POPULAR_MONTH,
  DELTA_POST_POPULAR_COURSES,
  DELTA_POST_POPULAR_ELEMENTS
} from "../../consts";

const defaultState = {
  started: false,
  finished: false,
  failed: false,
  number_files: null,
  number_downloads: null,
  number_courses_with_content: null,
  number_new_elements: null,

  latest_elements: [],
  courses_last_visited: [],
  courses_last_downloaded: [],

  elements_most_popular: [],
  courses_most_popular: [],

  elements_most_popular_loading: false,
  courses_most_popular_loading: false,

  user_preferences: {
    [DELTA_POST_POPULAR_COURSES]: DELTA_MOST_POPULAR_MONTH,
    [DELTA_POST_POPULAR_ELEMENTS]: DELTA_MOST_POPULAR_MONTH,
  },
};

export const boxes = (state = defaultState, action) => {
  switch (action.type) {

    case FRONTPAGE_BOXES_FETCH_STARTED:
      return {
        ...state,
        boxes: {
          ...state.boxes,
          started: true,
        }
      };

    case FRONTPAGE_BOXES_FETCH_FINISHED:
      return {
        ...state,

        boxes: {
          ...state.boxes,
          started: false,
          finished: true,
          ...mapFrontpageBoxes(action.data)
        },
      };

    case FRONTPAGE_BOXES_FETCH_FAILED:
      return {
        ...state,

        boxes: {
          ...state.boxes,
          failed: true,
          started: false,
        },
      };

    case FRONTPAGE_DELTA_CHANGE_STARTED:
      if (action.delta === DELTA_POST_POPULAR_ELEMENTS) {
        return {
          ...state,
          elements_most_popular_loading: true,
        }
      }

      return {
        ...state,
        courses_most_popular_loading: true,
      };

    case FRONTPAGE_DELTA_CHANGE_FAILED:
      return {
        ...state,
        elements_most_popular_loading: false,
        courses_most_popular_loading: false,
      };

    case FRONTPAGE_DELTA_CHANGE_FINISHED:
      if (action.delta === DELTA_POST_POPULAR_ELEMENTS) {
        return {
          ...state,
          elements_most_popular: action.data,
          elements_most_popular_loading: false,

          user_preferences: {
            ...state.user_preferences,
            [DELTA_POST_POPULAR_ELEMENTS]: action.value
          }
        };
      }

      return {
        ...state,
        courses_most_popular: action.data,
        courses_most_popular_loading: false,

        user_preferences: {
          ...state.user_preferences,
          [DELTA_POST_POPULAR_COURSES]: action.value
        }
      };

    default:
      return state
  }
};
