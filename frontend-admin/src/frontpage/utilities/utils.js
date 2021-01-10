import {
  DELTA_MOST_POPULAR_TODAY,
  DELTA_MOST_POPULAR_WEEK,
  DELTA_MOST_POPULAR_MONTH,
  DELTA_MOST_POPULAR_YEAR,
} from '../consts';

export const userPreferenceDeltaToString = preference => {
  switch (preference) {
    case DELTA_MOST_POPULAR_TODAY:
      return 'Siste 24 timer';
    case DELTA_MOST_POPULAR_WEEK:
      return 'Siste uke';
    case DELTA_MOST_POPULAR_MONTH:
      return 'Siste måned';
    case DELTA_MOST_POPULAR_YEAR:
      return 'Siste år';
    default:
      return 'Alltid';
  }
};