import {
  FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FAILED,
  FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FINISHED,
  FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_STARTED,
  FRONTPAGE_POPULAR_COURSES_FETCH_FAILED,
  FRONTPAGE_POPULAR_COURSES_FETCH_FINISHED,
  FRONTPAGE_POPULAR_COURSES_FETCH_STARTED,
} from './constants';

const defaultState = {
  started: false,
  finished: false,
  failed: false,
  courses: [],
};

export const popularCourses = (state = defaultState, action) => {
  switch (action.type) {

    case FRONTPAGE_POPULAR_COURSES_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case FRONTPAGE_POPULAR_COURSES_FETCH_FINISHED:
      return {
        ...state,
        started: false,
        finished: true,
        courses: action.courses,
      };

    case FRONTPAGE_POPULAR_COURSES_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    case FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_STARTED:
      return {
        ...state,
        started: true,
        finished: false,
        failed: false,
        courses: [],
      };

    case FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    case FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FINISHED:
      return {
        ...state,
        started: false,
        finished: true,
        courses: action.courses,
      };

    default:
      return state
  }
};
