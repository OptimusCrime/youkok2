import {
  UPDATE_SEARCH_FIELD,
  UPDATE_SEARCH_RESULTS,
  UPDATE_CURSOR_POSITION
} from "./constants";
import { ARROW_DOWN, ARROW_UP } from "../../consts";

export const updateSearchField = (value, courses) => dispatch => {
  // First, update the search cursor
  dispatch({ type: UPDATE_SEARCH_FIELD, value });

  // Filter the results
  const results = courses.filter(course => course.name.includes(value));
  dispatch({ type: UPDATE_SEARCH_RESULTS, results });
};

export const updateCursorPosition = (direction, cursorPosition, results) => dispatch => {
  if (results.length === 0) {
    return null;
  }

  const lastResultIndex = results.length - 1;

  // Handle set initial value
  if (cursorPosition === null) {
    if (direction === ARROW_DOWN) {
      return dispatch({
        type: UPDATE_CURSOR_POSITION,
        value: 0,
        display: results[0].name
      });
    }

    return dispatch({
      type: UPDATE_CURSOR_POSITION,
      value: lastResultIndex,
      display: results[lastResultIndex].name
    });
  }

  // Handle regular increases
  if (direction === ARROW_DOWN && cursorPosition !== lastResultIndex) {
    return dispatch({
      type: UPDATE_CURSOR_POSITION,
      value: cursorPosition + 1,
      display: results[cursorPosition + 1].name
    });
  }
  if (direction === ARROW_UP && cursorPosition !== 0) {
    return dispatch({
      type: UPDATE_CURSOR_POSITION,
      value: cursorPosition - 1,
      display: results[cursorPosition - 1].name
    });
  }

  // Otherwise, reset the cursor, off the grid
  return dispatch({
    type: UPDATE_CURSOR_POSITION,
    value: null,
  });
};