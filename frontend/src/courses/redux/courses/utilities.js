import {COLUMN_CODE, COURSES_PER_PAGE, ORDER_DESC} from "../../constants";

export const sortCourses = (column, order, page) => {
  if (!window.COURSES_LOOKUP) {
    // This should have thrown an error earlier, but just to be on the safe side
    return [];
  }

  const sorted = window.COURSES_LOOKUP
    .slice()
    .sort((a, b) => sortCoursesKey(column, order, a, b));

  return sorted.slice(page * COURSES_PER_PAGE, (page + 1) * COURSES_PER_PAGE);
};

const sortCoursesKey = (column, order, a, b) => {
  if (column === COLUMN_CODE) {
    if (order === ORDER_DESC) {
      return sortCoursesKeyValue(a.code, b.code);
    }

    return sortCoursesKeyValue(a.code, b.code) * -1;
  }

  if (order === ORDER_DESC) {
    return sortCoursesKeyValue(a.name, b.name);
  }

  return sortCoursesKeyValue(a.name, b.name) * -1;
};

const sortCoursesKeyValue = (a, b) => {
  if (a > b) return 1;
  if (a < b) return -1;
  return 0;
};
