import {
  fetchAdminHomeGraphRest,
} from '../../api';
import {
  ADMIN_HOME_GRAPH_FETCH_STARTED,
  ADMIN_HOME_GRAPH_FETCH_FINISHED,
  ADMIN_HOME_GRAPH_FETCH_FAILED,
} from './constants';

export const fetchAdminHomeGraph = () => dispatch => {
  dispatch({ type: ADMIN_HOME_GRAPH_FETCH_STARTED });

  fetchAdminHomeGraphRest()
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_HOME_GRAPH_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);
      dispatch({ type: ADMIN_HOME_GRAPH_FETCH_FAILED })
    });
};
