import { fetchArchiveRest } from '../../api';
import {
  ARCHIVE_FETCH_FAILED,
  ARCHIVE_FETCH_FINISHED,
  ARCHIVE_FETCH_STARTED,
} from './constants';
import {TEMP_CACHE_TIMEOUT_IN_SECONDS} from "../../consts";
import {setItem} from "../../../common/local-storage";
import {getLocalStorageKeyForCurrentUri} from "../../utilities";

export const fetchArchive = () => dispatch => {
  dispatch({ type: ARCHIVE_FETCH_STARTED });

  fetchArchiveRest()
    .then(response => response.json())
    .then(data => {
      dispatch({ type: ARCHIVE_FETCH_FINISHED, data: data });

      const timeout = new Date().getTime() + (TEMP_CACHE_TIMEOUT_IN_SECONDS * 1000);

      setItem(
        getLocalStorageKeyForCurrentUri(),
        JSON.stringify({
          data,
          timeout
        })
      );
    })
    .catch(() => dispatch({ type: ARCHIVE_FETCH_FAILED }));
};
