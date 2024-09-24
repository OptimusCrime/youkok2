import sha1 from "sha1";

import {getItem, setItem} from "./local-storage";
import {HTTP_NOT_MODIFIER} from "./constants";

// Only attempt to refresh the courses every 24 hours
const CHECK_INTERVAL = 60 * 60 * 24;

const LAST_CHECKED_KEY = 'courses_lookup_last_checked';
const CHECKSUM_KEY = 'courses_lookup_checksum';
const COURSES_LOOKUP_KEY = 'courses_lookup';

export const getCourses = () => {
  const courses = getItem(COURSES_LOOKUP_KEY);

  if (courses === null) {
    return null;
  }

  try {
    return JSON.parse(courses);
  } catch (e) {
    return null;
  }
}

const getCurrentTimestamp = () => parseInt(new Date().getTime() / 1000);

export const verifyLookup = () => {
  const lastChecked = getItem(LAST_CHECKED_KEY) || null;
  const checksum = getItem(CHECKSUM_KEY) || null;
  const courses = getCourses();

  // Force recheck if any keys are missing
  if (lastChecked === null || checksum === null || courses === null) {
    return refreshCourses(null);
  }

  // Check if we exceeded the interval
  const currentTimestamp = getCurrentTimestamp();
  if (currentTimestamp >= (parseInt(lastChecked) + CHECK_INTERVAL)) {
    return refreshCourses(checksum);
  }
};

export async function refreshCourses(checksum) {
  const response = await fetch(`/rest/courses${checksum === null ? '' : `?checksum=${checksum}`}`, {
    method: 'GET',
  });

  const currentTimestamp = getCurrentTimestamp().toString();
  if (response.status === HTTP_NOT_MODIFIER) {
    // We have the correct checksum still, just update the timestamp
    setItem(LAST_CHECKED_KEY, currentTimestamp);
    return;
  }

  await response.text()
    .then(text => {
      setItem(LAST_CHECKED_KEY, currentTimestamp);
      setItem(CHECKSUM_KEY, sha1(text))
      setItem(COURSES_LOOKUP_KEY, text);
    });
}
