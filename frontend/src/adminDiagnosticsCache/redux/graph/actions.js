import {
  fetchAdminDiagnosticsCacheRest,
} from '../../api';
import {
  ADMIN_DIAGNOSTICS_CACHE_FETCH_STARTED,
  ADMIN_DIAGNOSTICS_CACHE_FETCH_FINISHED,
  ADMIN_DIAGNOSTICS_CACHE_FETCH_FAILED,
} from './constants';

export const fetchAdminDiagnosticsCache = () => dispatch => {
  dispatch({ type: ADMIN_DIAGNOSTICS_CACHE_FETCH_STARTED });

  fetchAdminDiagnosticsCacheRest()
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_DIAGNOSTICS_CACHE_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);
      dispatch({ type: ADMIN_DIAGNOSTICS_CACHE_FETCH_FAILED })
    });
};
