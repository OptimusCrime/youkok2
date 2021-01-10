import {
  FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FAILED, FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FINISHED,
  FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_STARTED,
  FRONTPAGE_POPULAR_ELEMENTS_FETCH_FAILED,
  FRONTPAGE_POPULAR_ELEMENTS_FETCH_FINISHED,
  FRONTPAGE_POPULAR_ELEMENTS_FETCH_STARTED,
} from './constants';
import {DEFAULT_MOST_POPULAR_ELEMENTS_DELTA} from "../../consts";

const defaultState = {
  started: false,
  finished: false,
  failed: false,
  elements: [],
};

export const popularElements = (state = defaultState, action) => {
  switch (action.type) {

    case FRONTPAGE_POPULAR_ELEMENTS_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case FRONTPAGE_POPULAR_ELEMENTS_FETCH_FINISHED:
      return {
        ...state,
        started: false,
        finished: true,
        elements: action.elements,
      };

    case FRONTPAGE_POPULAR_ELEMENTS_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    case FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_STARTED:
      return {
        ...state,
        started: true,
        finished: false,
        failed: false,
        elements: [],
      };

    case FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    case FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FINISHED:
      return {
        ...state,
        started: false,
        finished: true,
        elements: action.elements,
      };

    default:
      return state
  }
};
