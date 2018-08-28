// https://stackoverflow.com/a/2901298/921563
export const formatNumbers = number => number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

export const loading = (started, finished) => (!started && !finished) || (started && !finished);