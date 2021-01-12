import {
  COLUMN_CODE,
  DEFAULT_COLUMN,
  DEFAULT_ORDER,
  DEFAULT_SEARCH,
  DEFAULT_SHOW_ONLY_NOT_EMPTY,
  ORDER_DESC
} from "../../constants";
import {getCourses} from "../../../common/coursesLookup";

export const defaultCourses = () =>
  updateCourses({
    column: DEFAULT_COLUMN,
    order: DEFAULT_ORDER,
    search: DEFAULT_SEARCH,
    showOnlyNotEmpty: DEFAULT_SHOW_ONLY_NOT_EMPTY,
  });

export const updateCourses = ({ column, order, search, showOnlyNotEmpty}) => {
  if (!getCourses()) {
    return [];
  }

  return sortCoursesList(filterSearch(search, showOnlyNotEmpty), column, order);
};

const filterSearch = (search, showOnlyNotEmpty) => getCourses().filter(course => {
    if (showOnlyNotEmpty && course.empty) {
      return false;
    }

    // No need to filter empty search
    if (search === DEFAULT_SEARCH) {
      return true;
    }

    return search
      .split(' ')
      .filter(word => /\w+/.test(word))
      .map(word => word.toLowerCase())
      .every(word => course.name.toLowerCase().includes(word) || course.code.toLowerCase().includes(word));
  });

const sortCoursesList = (courses, column, order) => {
  const sorted = courses
    .slice()
    .sort((a, b) => sortCoursesKey(column, a, b));

  if (order === ORDER_DESC) {
    return sorted;
  }

  return sorted.reverse();
};

const sortCoursesKey = (column, a, b) => {
  if (column === COLUMN_CODE) {
    return sortCoursesKeyValue(a.code, b.code);
  }

  return sortCoursesKeyValue(a.name.trim(), b.name.trim());
};

const sortCoursesKeyValue = (a, b) => {
  if (a > b) return 1;
  if (a < b) return -1;
  return 0;
};
