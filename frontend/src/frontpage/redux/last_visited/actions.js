import {fetchFrontPageLastDownloadedRest} from '../../api';
import {
  FRONTPAGE_LAST_DOWNLOADED_FETCH_FAILED,
  FRONTPAGE_LAST_DOWNLOADED_FETCH_FINISHED,
  FRONTPAGE_LAST_DOWNLOADED_FETCH_STARTED,
} from './constants';

export const fetchFrontPageLastDownloaded = () => dispatch => {
  dispatch({ type: FRONTPAGE_LAST_DOWNLOADED_FETCH_STARTED });

  fetchFrontPageLastDownloadedRest()
    .then(response => response.json())
    .then(response => dispatch({ type: FRONTPAGE_LAST_DOWNLOADED_FETCH_FINISHED, data: response.data }))
    .catch(e => {
      console.error(e);

      dispatch({ type: FRONTPAGE_LAST_DOWNLOADED_FETCH_FAILED })
    });
};

