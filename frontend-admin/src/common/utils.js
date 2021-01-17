import { format } from 'date-fns';
import nb from 'date-fns/locale/nb';

// https://stackoverflow.com/a/2901298/921563
export const formatNumber = number => number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

// Improved from https://stackoverflow.com/a/9461657/921563
export const formatThousandNumber = number => {
  if (number <= 999) {
    return number;
  }

  return number % 1000 === 0 ? ((number / 1000) + 'k') : ((number / 1000).toFixed(1) + 'k');
};

export const loading = (started, finished) => (!started && !finished) || (started && !finished);

export const randomBetween = (lower, higher) => Math.floor(Math.random() * higher) + lower;

export const fromDatabaseDateToJavaScriptDate = datetime => {
  const [date, time] = datetime.split(' ');

  const [year, month, day] = date.split('-').map(member => parseInt(member));
  const [hour, minute, second] = time.split(':').map(member => parseInt(member));

  return new Date(year, month - 1, day, hour, minute, second);
};

export const formatJavaScriptDateHumanReadable = date => format(date, 'D. MMM YYYY @ HH:mm:ss', { locale: nb });
