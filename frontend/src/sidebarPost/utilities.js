// ... https://stackoverflow.com/a/5717133/921563
const validUrlPattern = new RegExp('^(https?:\\/\\/)?' + // protocol
  '((([a-zæøå\\d]([a-zæøå\\d-]*[a-zæøå\\d])*)\\.)+[a-zæøå]{2,}|' + // domain name
  '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
  '(\\:\\d+)?(\\/[-a-zæøå\\d%_.~+]*)*' + // port and path
  '(\\?[;&a-zæøå\\d%_.~+=-]*)?' + // query string
  '(\\#[-a-zæøå\\d_]*)?$', 'i'); // fragment locator

export const isValidUrl = str => !!validUrlPattern.test(str);

export const isValidFile = file => {
  const fileNameSplit = file.name.split('.');
  if (fileNameSplit.length === 0 || fileNameSplit.length === 1) {
    return false;
  }

  if (!window.SITE_DATA.archive_valid_file_types.includes(fileNameSplit[fileNameSplit.length - 1])) {
    return false;
  }

  return file.size <= window.SITE_DATA.archive_max_file_size_bytes;
};

// https://stackoverflow.com/a/20732091/921563
export const humanReadableFileSize = size => {
  if (size === 0) {
    return '0 B';
  }

  const i = Math.floor(Math.log(size) / Math.log(1024));
  return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
};

export const calculateProgress = files => (files.filter(file => file.finished).length / files.length) * 100;
