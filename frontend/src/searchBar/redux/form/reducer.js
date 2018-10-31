import {
  UPDATE_SEARCH_FIELD,
  UPDATE_SEARCH_RESULTS,
  UPDATE_CURSOR_POSITION
} from "./constants";
import { resultsAreIdentical } from "./util";

const defaultState = {
  input_raw: '',
  input_display: '',

  results: [],
  cursor: null,
};

const form = (state = defaultState, action) => {
  switch (action.type) {

    case UPDATE_SEARCH_FIELD:
      return {
        ...state,
        input_raw: action.value,
        input_display: action.value,
      };

    case UPDATE_SEARCH_RESULTS:
      // If the new results are identical to the old ones, do not reset the cursor
      if (resultsAreIdentical(state.results, action.results)) {
        return state;
      }

      return {
        ...state,
        results: action.results,
        cursor: null
      };

    case UPDATE_CURSOR_POSITION:
      if (action.value === null) {
        // Reset the display
        return {
          ...state,
          cursor: action.value,
          input_display: state.input_raw
        };
      }

      return {
        ...state,
        cursor: action.value,
        input_display: action.display
      };

    default:
      return state
  }
};

export default form;