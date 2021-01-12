import {
  COURSE_CHANGE_CHECKBOX,
  COURSES_CHANGE_ORDER,
  COURSES_CHANGE_PAGE,
  COURSES_LOOKUP_LOADED,
  COURSES_UPDATE_SEARCH,
} from "./constants";
import {
  DEFAULT_PAGE,
  DEFAULT_COLUMN,
  DEFAULT_ORDER,
  DEFAULT_SHOW_ONLY_NOT_EMPTY,
  DEFAULT_SEARCH
} from "../../constants";
import {defaultCourses, updateCourses} from "./utilities";
import {getCourses} from "../../../common/coursesLookup";

const defaultState = {
  loaded: getCourses() !== null,
  column: DEFAULT_COLUMN,
  order: DEFAULT_ORDER,
  page: DEFAULT_PAGE,
  search: DEFAULT_SEARCH,
  showOnlyNotEmpty: DEFAULT_SHOW_ONLY_NOT_EMPTY,

  courses: defaultCourses()
};

export const courses = (state = defaultState, action) => {
  switch (action.type) {

    case COURSES_LOOKUP_LOADED:
      return {
        ...state,
        loaded: true,

        courses: defaultCourses()
      };

    case COURSES_CHANGE_ORDER:
      return {
        ...state,

        column: action.column,
        order: action.order,
        page: DEFAULT_PAGE,

        courses: updateCourses({
          column: action.column,
          order: action.order,
          search: state.search,
          showOnlyNotEmpty: state.showOnlyNotEmpty,
        }),
      };

    case COURSES_CHANGE_PAGE:
      return {
        ...state,

        page: action.page,

        courses: updateCourses({
          column: state.column,
          order: state.order,
          search: state.search,
          showOnlyNotEmpty: state.showOnlyNotEmpty,
        }),
      };

    case COURSES_UPDATE_SEARCH:
      return {
        ...state,

        search: action.value,
        page: DEFAULT_PAGE,

        courses: updateCourses({
          column: state.column,
          order: state.order,
          search: action.value,
          showOnlyNotEmpty: state.showOnlyNotEmpty,
        }),
      };

    case COURSE_CHANGE_CHECKBOX:
      return {
        ...state,

        page: DEFAULT_PAGE,
        showOnlyNotEmpty: !state.showOnlyNotEmpty,

        courses: updateCourses({
          column: state.column,
          order: state.order,
          search: state.search,
          showOnlyNotEmpty: !state.showOnlyNotEmpty,
        }),
      };

    default:
      return state
  }
};
