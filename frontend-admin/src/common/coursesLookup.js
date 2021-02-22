import {adminFetch} from "./fetch";
import {ADMIN_COURSES_LOADED} from "../searchBar/redux/config/constants";

// These methods are just here to allow us to reuse the same code in both contexts
export const getCourses = () => null;

export const verifyLookup = () => {};

export const refreshCourses = () => dispatch =>
  adminFetch('/rest/admin/lookup')
    .then(response => response.json())
    .then(response => dispatch({ type: ADMIN_COURSES_LOADED, data: response.data }))
    .catch(() => {
      alert('Something went wrong');
    });
