import { fetchFrontPagePopularElementsRest } from '../../api';
import {
  FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FAILED,
  FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FINISHED,
  FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_STARTED,
  FRONTPAGE_POPULAR_ELEMENTS_FETCH_FAILED,
  FRONTPAGE_POPULAR_ELEMENTS_FETCH_FINISHED,
  FRONTPAGE_POPULAR_ELEMENTS_FETCH_STARTED,
} from './constants';
import {
  DEFAULT_MOST_POPULAR_ELEMENTS_DELTA,
  DELTA_POST_POPULAR_ELEMENTS
} from "../../consts";
import { getItem, setItem } from "../../../common/local-storage";

export const fetchFrontPagePopularElements = () => dispatch => {
  dispatch({ type: FRONTPAGE_POPULAR_ELEMENTS_FETCH_STARTED });

  const delta = getItem(DELTA_POST_POPULAR_ELEMENTS) || DEFAULT_MOST_POPULAR_ELEMENTS_DELTA;

  fetchFrontPagePopularElementsRest(delta)
    .then(response => response.json())
    .then(response => dispatch({
        type: FRONTPAGE_POPULAR_ELEMENTS_FETCH_FINISHED,
        elements: response.elements,
      })
    )
    .catch(() => dispatch({ type: FRONTPAGE_POPULAR_ELEMENTS_FETCH_FAILED }));
};

export const updateFrontpagePopularElements = delta => dispatch => {
  setItem(DELTA_POST_POPULAR_ELEMENTS, delta);

  dispatch({ type: FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_STARTED });

  fetchFrontPagePopularElementsRest(delta)
    .then(response => response.json())
    .then(response => dispatch({
      type: FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FINISHED,
      elements: response.elements,
    }))
    .catch(() => dispatch({ type: FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FAILED }));
};
