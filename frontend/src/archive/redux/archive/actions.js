import { fetchArchiveRest } from '../../api';
import {
  ARCHIVE_FETCH_FAILED,
  ARCHIVE_FETCH_FINISHED,
  ARCHIVE_FETCH_STARTED,
} from './constants';

export const fetchArchive = () => dispatch => {
  dispatch({ type: ARCHIVE_FETCH_STARTED });

  fetchArchiveRest()
    .then(response => response.json())
    .then(data => dispatch({ type: ARCHIVE_FETCH_FINISHED, data: data }))
    .catch(() => dispatch({ type: ARCHIVE_FETCH_FAILED }));
};