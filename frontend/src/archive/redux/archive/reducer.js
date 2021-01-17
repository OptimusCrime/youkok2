import {
  ARCHIVE_DATA_FETCH_STARTED,
  ARCHIVE_DATA_FETCH_FINISHED,
  ARCHIVE_DATA_FETCH_FAILED,
  ARCHIVE_CONTENT_FETCH_STARTED,
  ARCHIVE_CONTENT_FETCH_FINISHED,
  ARCHIVE_CONTENT_FETCH_FAILED,
} from "./constants";

const defaultState = {
  id: null,
  course_id: null,
  empty: null,
  parents: null,
  title: null,
  sub_title: null,
  valid_file_types: null,
  max_file_size_bytes: null,
  requested_deletion: null,
  html_title: null,
  html_description: null,

  archive: {},

  data_started: false,
  data_finished: false,
  data_failed: false,

  content_started: false,
  content_finished: false,
  content_failed: false,
};

export const archive = (state = defaultState, action) => {
  switch (action.type) {
    case ARCHIVE_DATA_FETCH_STARTED:
      return {
        ...state,
        data_started: true,
      };

    case ARCHIVE_DATA_FETCH_FAILED:
      return {
        ...state,
        data_started: false,
        data_failed: true,
      };

    case ARCHIVE_DATA_FETCH_FINISHED:
      return {
        ...state,
        data_started: false,
        data_finished: true,
        ...action.data,
        course_id: action.data.parents[0].id,
      };

    case ARCHIVE_CONTENT_FETCH_STARTED:
      return {
        ...state,
        content_started: true,
      };

    case ARCHIVE_CONTENT_FETCH_FINISHED:
      return {
        ...state,
        content_started: false,
        content_finished: true,
        archive: action.data,
      };

    case ARCHIVE_CONTENT_FETCH_FAILED:
      return {
        ...state,
        content_started: false,
        content_failed: true,
      };

    default:
      return state
  }
};
