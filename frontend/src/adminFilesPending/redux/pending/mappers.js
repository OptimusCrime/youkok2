import {TYPE_DIRECTORY, TYPE_FILE, TYPE_LINK} from "../../../common/types";

export const mapPendingData = data => data.map(entry => ({
  content: entry,
  pending: mapPendingFiles(entry),
}));

const mapPendingFiles = data => {
  const childrenPending = data.children
    .filter(child => (child.type === TYPE_FILE || child.type === TYPE_LINK) && child.pending);

  const childrenDirectories = data.children.filter(child => child.type === TYPE_DIRECTORY);

  // No directory children, return the list of current pending
  if (childrenDirectories.length === 0) {
    return childrenPending;
  }

  return childrenPending.concat(
    childrenDirectories.flatMap(child => mapPendingFiles(child))
  );
};

