import {ADMIN_COURSES_LOADED, SET_MODE} from "./constants";

const defaultState = {
  mode: 'site',
  admin_loaded: false,
  admin_courses: [],
};

// This reducer is just hacked together to get the same module to work copy&pasting in both admin and site contexts...
export const config = (state = defaultState, action) => {
  switch (action.type) {
    case SET_MODE:
      return {
        ...state,
        mode: action.mode,
      }

    case ADMIN_COURSES_LOADED:
      return {
        ...state,
        admin_loaded: true,
        admin_courses: action.data,
      }

    default:
      return state
  }
};
