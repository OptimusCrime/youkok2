import {
  fetchSidebarHistoryRest,
} from '../../api';
import {
  SIDEBAR_HISTORY_FETCH_FAILED,
  SIDEBAR_HISTORY_FETCH_FINISHED,
  SIDEBAR_HISTORY_FETCH_STARTED,
} from './constants';

export const fetchSidebarHistory = id => dispatch => {
  dispatch({ type: SIDEBAR_HISTORY_FETCH_STARTED });

  fetchSidebarHistoryRest(id)
    .then(response => response.json())
    .then(response => dispatch({ type: SIDEBAR_HISTORY_FETCH_FINISHED, data: response.data }))
    .catch(() => dispatch({ type: SIDEBAR_HISTORY_FETCH_FAILED }));
};
