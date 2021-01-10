import {TEMP_CACHE_PREFIX} from "./consts";
import {getAllKeys, getItem, removeItem} from "../common/local-storage";

export const removeExpiredCache = () => {
  getAllKeys()
    .filter(key => key.startsWith(TEMP_CACHE_PREFIX))
    .forEach(key => {
      const data = JSON.parse(getItem(key));

      if (data.timeout && new Date().getTime() > data.timeout) {
        removeItem(key);
      }
    });
};

export const getLocalStorageKeyForCurrentUri = () =>
  `${TEMP_CACHE_PREFIX}${window.location.pathname.replace(/\//g, '_')}`;
