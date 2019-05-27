import {
  fetchSidebarHistoryRest,
} from '../../api';
import {
  SIDEBAR_HISTORY_FETCH_FAILED,
  SIDEBAR_HISTORY_FETCH_FINISHED,
  SIDEBAR_HISTORY_FETCH_STARTED,
} from './constants';

export const fetchSidebarHistory = () => dispatch => {
  dispatch({ type: SIDEBAR_HISTORY_FETCH_STARTED });

  fetchSidebarHistoryRest()
    .then(response => response.json())
    .then(response => dispatch({ type: SIDEBAR_HISTORY_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);

      dispatch({ type: SIDEBAR_HISTORY_FETCH_FAILED })
    });
};
