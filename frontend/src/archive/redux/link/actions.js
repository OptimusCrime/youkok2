import {fetchTitleFromUrlRest, postLinkRest} from "../../api";
import {
  SIDEBAR_POST_LINK_TITLE_FETCH_FAILED,
  SIDEBAR_POST_LINK_TITLE_FETCH_FINISHED,
  SIDEBAR_POST_LINK_TITLE_FETCH_STARTED,
  SIDEBAR_POST_LINK_TITLE_POST_ERROR,
  SIDEBAR_POST_LINK_TITLE_POST_FINISHED,
  SIDEBAR_POST_LINK_TITLE_POST_STARTED
} from "./constants";
import {LINK_POST_ERROR_BACKEND} from "../../../sidebarPost/constants";

export const fetchTitleFromUrl = url => dispatch => {
  dispatch({ type: SIDEBAR_POST_LINK_TITLE_FETCH_STARTED });

  fetchTitleFromUrlRest(url)
    .then(response => response.json())
    .then(response => dispatch({ type: SIDEBAR_POST_LINK_TITLE_FETCH_FINISHED, title: response.title }))
    .catch(err => {
      if (err.name !== 'AbortError') {
        dispatch({type: SIDEBAR_POST_LINK_TITLE_FETCH_FAILED})
      }
    });
};

export const postLink = (id, url, title) => dispatch => {
  dispatch({ type: SIDEBAR_POST_LINK_TITLE_POST_STARTED });

  postLinkRest(id, url, title)
    .then(response => response.json())
    .then(response => dispatch({ type: SIDEBAR_POST_LINK_TITLE_POST_FINISHED, title: response.title }))
    .catch(() => dispatch({type: SIDEBAR_POST_LINK_TITLE_POST_ERROR, reason: LINK_POST_ERROR_BACKEND}));
};
