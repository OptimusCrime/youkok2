import {SEARCH_QUERY_IDENTIFIER} from "../common/constants";

export const queryPresentInUrl = () => window.location.href.includes('?');

export const getSearchFromUrl = () => {
  const urlSplit = window.location.href.split('?');

  if (urlSplit.length < 2) {
    return null;
  }

  // Use second element in split, as a guard in case there are multiple ?'s in the URL for some reason...
  const searchQuery = urlSplit[1]
    .split('&')
    .filter(fragment => fragment !== null && fragment.length > 0 && fragment.includes('='))
    .map(fragment => {
      const [key, value] = fragment.split('=');

      if (key !== SEARCH_QUERY_IDENTIFIER) {
        return null;
      }

      // Some minor protection here, even though it does not seem to be necessary
      return decodeURI(value).replace('<', '').replace('>', '');
    })
    .filter(param => param !== null);

  if (searchQuery.length !== 1) {
    return null;
  }

  return searchQuery[0];
};

