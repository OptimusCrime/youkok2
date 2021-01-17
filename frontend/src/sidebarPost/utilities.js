// https://stackoverflow.com/a/20732091/921563
export const humanReadableFileSize = size => {
  if (size === 0) {
    return '0 B';
  }

  const i = Math.floor(Math.log(size) / Math.log(1024));
  return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
};

export const calculateProgress = files =>
  ((files.filter(file => file.finished).length + files.filter(file => file.failed).length) / files.length) * 100;
