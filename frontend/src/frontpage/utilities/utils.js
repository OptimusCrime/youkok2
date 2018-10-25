import {
  DELTA_MOST_POPULAR_TODAY,
  DELTA_MOST_POPULAR_WEEK,
  DELTA_MOST_POPULAR_MONTH,
  DELTA_MOST_POPULAR_YEAR,
} from '../consts';

export const userPreferenceDeltaToString = preference => {
  switch (preference) {
    case DELTA_MOST_POPULAR_TODAY:
      return 'I dag';
    case DELTA_MOST_POPULAR_WEEK:
      return 'Denne uka';
    case DELTA_MOST_POPULAR_MONTH:
      return 'Denne måneden';
    case DELTA_MOST_POPULAR_YEAR:
      return 'Dette året';
    default:
      return 'Alltid';
  }
};