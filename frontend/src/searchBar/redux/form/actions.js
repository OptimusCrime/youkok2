import {
  UPDATE_SEARCH_FIELD,
  UPDATE_SEARCH_RESULTS,
  UPDATE_CURSOR_POSITION,
  CLOSE_SEARCH_RESULTS
} from "./constants";
import {
  ARROW_DOWN,
  ARROW_UP,
  MAX_RESULTS
} from "../../constants";
import { selectedCourseToSearchBarText } from "./util";

export const updateSearchField = (search, courses) => dispatch => {
  const searchWords = search.split(' ');

  // First, update the search cursor
  dispatch({ type: UPDATE_SEARCH_FIELD, value: search });

  // Filter the results
  const results = courses.filter(course => {
    const wholeName = `${course.code.toLowerCase()} ${course.name.toLowerCase()}`;

    // If the search is just one word, match it directly
    if (searchWords.length === 1) {
      return wholeName.includes(searchWords[0]);
    }

    // Search consists of multiple words, we need to match all
    return searchWords.every(searchWord => wholeName.includes(searchWord));

  });

  if (results.length <= MAX_RESULTS) {
    return dispatch({ type: UPDATE_SEARCH_RESULTS, results });
  }

  return dispatch({ type: UPDATE_SEARCH_RESULTS, results: results.slice(0, MAX_RESULTS) });
};

export const updateCursorPosition = (direction, cursorPosition, results) => {
  if (results.length === 0) {
    return null;
  }

  const lastResultIndex = results.length - 1;

  // Handle set initial value
  if (cursorPosition === null) {
    if (direction === ARROW_DOWN) {
      return ({
        type: UPDATE_CURSOR_POSITION,
        value: 0,
        display: selectedCourseToSearchBarText(results[0])
      });
    }

    return ({
      type: UPDATE_CURSOR_POSITION,
      value: lastResultIndex,
      display: selectedCourseToSearchBarText(results[lastResultIndex])
    });
  }

  // Handle regular increases
  if (direction === ARROW_DOWN && cursorPosition !== lastResultIndex) {
    return ({
      type: UPDATE_CURSOR_POSITION,
      value: cursorPosition + 1,
      display: selectedCourseToSearchBarText(results[cursorPosition + 1])
    });
  }
  if (direction === ARROW_UP && cursorPosition !== 0) {
    return ({
      type: UPDATE_CURSOR_POSITION,
      value: cursorPosition - 1,
      display: selectedCourseToSearchBarText(results[cursorPosition - 1])
    });
  }

  // Otherwise, reset the cursor, off the grid
  return ({
    type: UPDATE_CURSOR_POSITION,
    value: null,
  });
};
