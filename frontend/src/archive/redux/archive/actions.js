import {fetchArchiveContentRest, fetchArchiveDataRest,} from '../../api';
import {
  ARCHIVE_DATA_FETCH_STARTED,
  ARCHIVE_DATA_FETCH_FINISHED,
  ARCHIVE_DATA_FETCH_FAILED,
  ARCHIVE_CONTENT_FETCH_STARTED,
  ARCHIVE_CONTENT_FETCH_FINISHED,
  ARCHIVE_CONTENT_FETCH_FAILED,
} from './constants';
import {fetchSidebarHistory} from "../history/actions";

export const fetchArchiveData = params => dispatch => {
  dispatch({ type: ARCHIVE_DATA_FETCH_STARTED });

  const { course, path } = params;

  fetchArchiveDataRest(course, path)
    .then(response => response.json())
    .then(data => {
      dispatch({ type: ARCHIVE_DATA_FETCH_FINISHED, data: data });

      dispatch(fetchArchiveContent(data.id));

      // Run the related sidebars
      dispatch(fetchSidebarHistory(data.id));
    })
    .catch(() => dispatch({ type: ARCHIVE_DATA_FETCH_FAILED }));
};

export const fetchArchiveContent = id => dispatch => {
  dispatch({ type: ARCHIVE_CONTENT_FETCH_STARTED });

  fetchArchiveContentRest(id)
    .then(response => response.json())
    .then(data => dispatch({ type: ARCHIVE_CONTENT_FETCH_FINISHED, data: data }))
    .catch(() => dispatch({ type: ARCHIVE_CONTENT_FETCH_FAILED }));
};
