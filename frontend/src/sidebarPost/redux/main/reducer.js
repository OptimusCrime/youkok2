import {TYPE_NONE} from "../../constants";
import {SIDEBAR_POST_OPEN} from "./constants";

const defaultState = {
  open: TYPE_NONE
};

export const main = (state = defaultState, action) => {
  switch (action.type) {

    case SIDEBAR_POST_OPEN:
      return {
        ...state,
        open: action.value
      };

    default:
      return state
  }
};
