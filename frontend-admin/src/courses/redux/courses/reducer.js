import {
  COURSE_CHANGE_CHECKBOX,
  COURSES_CHANGE_ORDER, COURSES_CHANGE_PAGE, COURSES_UPDATE_SEARCH,
} from "./constants";
import {
  DEFAULT_PAGE,
  DEFAULT_COLUMN,
  DEFAULT_ORDER,
  DEFAULT_SHOW_ONLY_NOT_EMPTY,
  DEFAULT_SEARCH
} from "../../constants";
import {updateCourses} from "./utilities";

const defaultState = {
  column: DEFAULT_COLUMN,
  order: DEFAULT_ORDER,
  page: DEFAULT_PAGE,
  search: DEFAULT_SEARCH,
  showOnlyNotEmpty: DEFAULT_SHOW_ONLY_NOT_EMPTY,

  courses: updateCourses({
    column: DEFAULT_COLUMN,
    order: DEFAULT_ORDER,
    search: DEFAULT_SEARCH,
    showOnlyNotEmpty: DEFAULT_SHOW_ONLY_NOT_EMPTY,
  }),
};

export const courses = (state = defaultState, action) => {
  switch (action.type) {

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
