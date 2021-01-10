import { fetchFrontPagePopularCoursesRest } from '../../api';
import {
  FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FAILED,
  FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FINISHED,
  FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_STARTED,
  FRONTPAGE_POPULAR_COURSES_FETCH_FAILED,
  FRONTPAGE_POPULAR_COURSES_FETCH_FINISHED,
  FRONTPAGE_POPULAR_COURSES_FETCH_STARTED,
} from './constants';
import { getItem, setItem } from "../../../common/local-storage";
import {
  DEFAULT_MOST_POPULAR_COURSES_DELTA,
  DELTA_POST_POPULAR_COURSES,
} from "../../consts";

export const fetchFrontPagePopularCourses = () => dispatch => {
  dispatch({ type: FRONTPAGE_POPULAR_COURSES_FETCH_STARTED });

  const delta = getItem(DELTA_POST_POPULAR_COURSES) || DEFAULT_MOST_POPULAR_COURSES_DELTA;

  fetchFrontPagePopularCoursesRest(delta)
    .then(response => response.json())
    .then(response => dispatch({
        type: FRONTPAGE_POPULAR_COURSES_FETCH_FINISHED,
        courses: response.courses,
      })
    )
    .catch(() => dispatch({ type: FRONTPAGE_POPULAR_COURSES_FETCH_FAILED }));
};

export const updateFrontpagePopularCourses = delta => dispatch => {
  setItem(DELTA_POST_POPULAR_COURSES, delta);

  dispatch({ type: FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_STARTED });

  fetchFrontPagePopularCoursesRest(delta)
    .then(response => response.json())
    .then(response => dispatch({
      type: FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FINISHED,
      courses: response.courses,
    }))
    .catch(() => dispatch({ type: FRONTPAGE_POPULAR_COURSES_DELTA_CHANGE_FAILED }));
};
