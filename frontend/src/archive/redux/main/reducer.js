import {SIDEBAR_POST_OPEN} from "./constants";
import {TYPE_NONE} from "../../../sidebarPost/constants";

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
