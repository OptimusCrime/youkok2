import {
  COURSES_CHANGE_ORDER, COURSES_CHANGE_PAGE,
} from "./constants";
import {DEFAULT_PAGE, DEFAULT_SORT_COLUMN, DEFAULT_SORT_ORDER} from "../../constants";
import {sortCourses} from "./utilities";

const defaultState = {
  sortColumn: DEFAULT_SORT_COLUMN,
  sortOrder: DEFAULT_SORT_ORDER,
  page: DEFAULT_PAGE,

  courses: sortCourses(DEFAULT_SORT_COLUMN, DEFAULT_SORT_ORDER, DEFAULT_PAGE),
};

export const courses = (state = defaultState, action) => {
  switch (action.type) {

    case COURSES_CHANGE_ORDER:
      return {
        ...state,

        sortColumn: action.sortColumn,
        sortOrder: action.sortOrder,
        page: 0,

        courses: sortCourses(action.sortColumn, action.sortOrder, 0),
      };

    case COURSES_CHANGE_PAGE:
      return {
        ...state,

        page: action.page,

        courses: sortCourses(state.sortColumn, state.sortOrder, action.page),
      };

    default:
      return state
  }
};
