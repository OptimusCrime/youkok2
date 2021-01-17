export const splitUrlPath = () => {
  const paths = window.location.pathname
    .split('/')
    .filter(fragment => fragment.length > 0)
    .splice(1);

  return {
    course: paths[0],
    path: paths.splice(1).join('/')  }
}

// ... https://stackoverflow.com/a/5717133/921563
const validUrlPattern = new RegExp('^(https?:\\/\\/)?' + // protocol
  '((([a-zæøå\\d]([a-zæøå\\d-]*[a-zæøå\\d])*)\\.)+[a-zæøå]{2,}|' + // domain name
  '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
  '(\\:\\d+)?(\\/[-a-zæøå\\d%_.~+]*)*' + // port and path
  '(\\?[;&a-zæøå\\d%_.~+=-]*)?' + // query string
  '(\\#[-a-zæøå\\d_]*)?$', 'i'); // fragment locator

export const isValidUrl = str => !!validUrlPattern.test(str);

export const isValidFile = (valid_file_types, max_file_size_bytes, file) => {
  const fileNameSplit = file.name.split('.');
  if (fileNameSplit.length === 0 || fileNameSplit.length === 1) {
    return false;
  }

  if (!valid_file_types.includes(fileNameSplit[fileNameSplit.length - 1])) {
    return false;
  }

  return file.size <= max_file_size_bytes;
};

