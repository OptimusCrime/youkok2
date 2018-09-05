// https://stackoverflow.com/a/2901298/921563
export const formatNumber = number => number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

export const loading = (started, finished) => (!started && !finished) || (started && !finished);

export const randomBetween = (lower, higher) => Math.floor(Math.random() * higher) + lower;