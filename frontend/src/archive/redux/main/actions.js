import {SIDEBAR_POST_OPEN} from "./constants";
import {TYPE_NONE} from "../../../sidebarPost/constants";

export const changeOpen = (open, currentOpen) => {
  if (open === TYPE_NONE || open === currentOpen) {
    return {type: SIDEBAR_POST_OPEN, value: TYPE_NONE};
  }

  return {type: SIDEBAR_POST_OPEN, value: open};
};
