export const resultsAreIdentical = (oldList, newList) => {
  const oldIds = oldList.reduce((str, item) => str + '-' + item.id.toString(), '');
  const newIds = newList.reduce((str, item) => str + '-' + item.id.toString(), '');

  return oldIds === newIds;
};