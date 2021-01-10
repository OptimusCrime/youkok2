import {TYPE_COURSE} from "../../../common/types";

export const mapUpdateFile = (course, data) => ({
  id: data.id,
  name: data.name,
  slug: data.slug,
  uri: data.uri,
  parent: data.parent,
  empty: data.empty,
  checksum: data.checksum,
  size: data.size,
  directory: data.directory,
  pending: data.pending,
  deleted: data.deleted,
  link: data.link,
  requested_deletion: data.type === TYPE_COURSE ? data.requested_deletion : 0,

  course,
});
