import {getLocalStorageKeyForCurrentUri} from "./utilities";
import {getItem, keyExists} from "../common/local-storage";
import {fetchArchive} from "./redux/archive/actions";
import {ARCHIVE_FETCH_FINISHED} from "./redux/archive/constants";

export const initArchive = store => {
  if (!window.SITE_DATA.archive_empty) {
    const cacheKey = getLocalStorageKeyForCurrentUri();

    if (keyExists(cacheKey)) {
      const item = JSON.parse(getItem(cacheKey));

      if (item.data) {
        return store.dispatch({
          type: ARCHIVE_FETCH_FINISHED,
          data: item.data
        });
      }
    }

    store.dispatch(fetchArchive());
  }
};
