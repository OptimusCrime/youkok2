import {adminFetch} from "./fetch";

// These methods are just here to allow us to reuse the same code in both contexts
export const getCourses = () => null;

export const verifyLookup = () => {};

export const refreshCourses = () =>
  adminFetch('/rest/admin/lookup', {
    method: 'POST',
  }).then(response => response.json())
    .then(response => {
      console.log('Dispatch stuff in here');
    });
