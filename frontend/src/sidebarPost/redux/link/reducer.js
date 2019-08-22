import {
  SIDEBAR_POST_LINK_CHANGE, SIDEBAR_POST_LINK_RESET,
  SIDEBAR_POST_LINK_TITLE_CHANGE,
  SIDEBAR_POST_LINK_TITLE_FETCH_FAILED,
  SIDEBAR_POST_LINK_TITLE_FETCH_FINISHED,
  SIDEBAR_POST_LINK_TITLE_FETCH_STARTED,
  SIDEBAR_POST_LINK_TITLE_POST_ERROR,
  SIDEBAR_POST_LINK_TITLE_POST_FINISHED,
  SIDEBAR_POST_LINK_TITLE_POST_STARTED
} from "./constants";
import {isValidUrl} from "../../utilities";

const defaultState = {
  url: '',
  valid: false,
  title: '',
  error: null,
  title_empty: false,
  title_started: false,
  title_finished: false,
  title_failed: false,
  post_started: false,
  post_finished: false,
};

export const link = (state = defaultState, action) => {
  switch (action.type) {

    case SIDEBAR_POST_LINK_CHANGE:
      return {
        ...state,
        url: action.value,
        valid: isValidUrl(action.value),
        title_empty: false,
        title_failed: false, // Avoid displaying error message box
        error: null,
      };

    case SIDEBAR_POST_LINK_TITLE_CHANGE:
      return {
        ...state,
        title: action.value,
        title_empty: false,
        title_failed: false, // Avoid displaying error message box
        error: null,
      };

    case SIDEBAR_POST_LINK_TITLE_FETCH_STARTED:
      return {
        ...state,
        title_started: true,
        title_finished: false,
        title_failed: false,
        error: null,
      };

    case SIDEBAR_POST_LINK_TITLE_FETCH_FINISHED:
      return {
        ...state,
        title_empty: action.title.length === 0,
        title: action.title.length === 0 ? state.title : action.title,
        title_started: false,
        title_finished: true,
      };

    case SIDEBAR_POST_LINK_TITLE_FETCH_FAILED:
      return {
        ...state,
        title_empty: false,
        title_started: false,
        title_failed: true,
      };

    case SIDEBAR_POST_LINK_TITLE_POST_ERROR:
      return {
        ...state,
        error: action.reason,
        post_started: false,
        post_finished: false,
      };

    case SIDEBAR_POST_LINK_TITLE_POST_STARTED:
      return {
        ...state,
        post_started: true,
        post_finished: false,
      };

    case SIDEBAR_POST_LINK_TITLE_POST_FINISHED:
      return {
        ...defaultState,
        post_started: false,
        post_finished: true,
      };

    case SIDEBAR_POST_LINK_RESET:
      return defaultState;

    default:
      return state
  }
};
