import {fetchFrontPagePopularCoursesRest, updateFrontpageRest} from '../../api';
import {
  FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FAILED,
  FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FINISHED,
  FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_STARTED,
  FRONTPAGE_POPULAR_COURSES_FETCH_FAILED,
  FRONTPAGE_POPULAR_COURSES_FETCH_FINISHED,
  FRONTPAGE_POPULAR_COURSES_FETCH_STARTED,
} from './constants';

export const fetchFrontPagePopularCourses = () => dispatch => {
  dispatch({ type: FRONTPAGE_POPULAR_COURSES_FETCH_STARTED });

  fetchFrontPagePopularCoursesRest()
    .then(response => response.json())
    .then(response => dispatch(
      {
        type: FRONTPAGE_POPULAR_COURSES_FETCH_FINISHED,
        courses: response.courses,
        preference: response.preference,
      })
    )
    .catch(() => dispatch({ type: FRONTPAGE_POPULAR_COURSES_FETCH_FAILED }));
};

export const updateFrontpagePopularCourses = (delta, value) => dispatch => {
  dispatch({ type: FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_STARTED, delta, value });

  updateFrontpageRest(delta, value)
    .then(response => response.json())
    .then(response => dispatch({
      type: FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FINISHED,
      courses: response.courses,
      preference: response.preference,
    }))
    .catch(() => dispatch({ type: FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FAILED }));
};
