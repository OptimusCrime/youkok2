// ... https://stackoverflow.com/a/5717133/921563
const validUrlPattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
  '((([a-zæøå\\d]([a-zæøå\\d-]*[a-zæøå\\d])*)\\.)+[a-zæøå]{2,}|'+ // domain name
  '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
  '(\\:\\d+)?(\\/[-a-zæøå\\d%_.~+]*)*'+ // port and path
  '(\\?[;&a-zæøå\\d%_.~+=-]*)?'+ // query string
  '(\\#[-a-zæøå\\d_]*)?$','i'); // fragment locator

export const isValidUrl = str => !!validUrlPattern.test(str);
