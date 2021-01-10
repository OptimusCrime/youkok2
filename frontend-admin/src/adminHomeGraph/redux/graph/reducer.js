import {
  ADMIN_HOME_GRAPH_FETCH_STARTED,
  ADMIN_HOME_GRAPH_FETCH_FINISHED,
  ADMIN_HOME_GRAPH_FETCH_FAILED,
} from "./constants";
import {mapGraphData} from "./mappers";

const defaultState = {
  started: false,
  finished: false,
  failed: false,

  data: []
};

export const graph = (state = defaultState, action) => {
  switch (action.type) {

    case ADMIN_HOME_GRAPH_FETCH_STARTED:
      return {
        ...state,
        started: true,
      };

    case ADMIN_HOME_GRAPH_FETCH_FINISHED:
      return {
        ...state,
        data: mapGraphData(action.data),
        finished: true,
        started: false,
      };

    case ADMIN_HOME_GRAPH_FETCH_FAILED:
      return {
        ...state,
        failed: true,
        started: false,
      };

    default:
      return state
  }
};
