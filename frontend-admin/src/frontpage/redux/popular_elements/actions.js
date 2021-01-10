import {fetchFrontPagePopularElementsRest, updateFrontpageRest} from '../../api';
import {
  FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FAILED,
  FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FINISHED,
  FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_STARTED,
  FRONTPAGE_POPULAR_ELEMENTS_FETCH_FAILED,
  FRONTPAGE_POPULAR_ELEMENTS_FETCH_FINISHED,
  FRONTPAGE_POPULAR_ELEMENTS_FETCH_STARTED,
} from './constants';

export const fetchFrontPagePopularElements = () => dispatch => {
  dispatch({ type: FRONTPAGE_POPULAR_ELEMENTS_FETCH_STARTED });

  fetchFrontPagePopularElementsRest()
    .then(response => response.json())
    .then(response => dispatch(
      {
        type: FRONTPAGE_POPULAR_ELEMENTS_FETCH_FINISHED,
        elements: response.elements,
        preference: response.preference,
      })
    )
    .catch(() => dispatch({ type: FRONTPAGE_POPULAR_ELEMENTS_FETCH_FAILED }));
};

export const updateFrontpagePopularElements = (delta, value) => dispatch => {
  dispatch({ type: FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_STARTED, delta, value });

  updateFrontpageRest(delta, value)
    .then(response => response.json())
    .then(response => dispatch({
      type: FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FINISHED,
      elements: response.elements,
      preference: response.preference,
    }))
    .catch(() => dispatch({ type: FRONTPAGE_POPULAR_ELEMENTS_DELTA_CHANGE_FAILED }));
};
