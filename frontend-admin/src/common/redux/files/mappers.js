import {TYPE_DIRECTORY, TYPE_FILE, TYPE_LINK} from "../../types";

export const mapData = data => data.map(entry => mapEntry(entry));

export const mapDisabled = (course, data) => data.map(entry => ({
  ...entry,
  disabled: course === entry.course ? true : entry.disabled,
}));

export const mapUpdated = (updated, data) => data.map(entry => {
  if (entry.course !== updated.id) {
    return entry;
  }

  return mapEntry(updated);
});

const mapEntry = entry => ({
  content: mapContent(entry),
  pending: mapPendingData(entry),
  disabled: false,
  course: entry.id,
});

const mapContent = entry => ({
  ...entry,
  children: entry.children || []
});

const mapPendingData = data =>
  mapPendingDataChildren(data)
    .filter(entry => entry.pending === 1); // ugh, I know, but I am lazy and this was the easiest way to fix this

const mapPendingDataChildren = data => {
  if (!data.children) {
    return [];
  }

  const childrenPending = data.children
    .filter(child => (child.type === TYPE_FILE || child.type === TYPE_LINK) && child.pending);

  const childrenDirectories = data.children.filter(child => child.type === TYPE_DIRECTORY);

  // No directory children, return the list of current pending
  if (childrenDirectories.length === 0) {
    return childrenPending;
  }

  return childrenPending.concat(
    childrenDirectories.flatMap(child => mapPendingDataChildren(child))
  );
};

