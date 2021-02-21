import { adminFetch } from "./fetch";

export const postAdminCreateDirectoryRest = (directory, course, value) => adminFetch('/rest/admin/files/directory', {
  body: JSON.stringify({
    directory,
    course,
    value
  }),
  method: 'PUT',
});

export const putAdminEditFileRest = (id, data) => adminFetch(`/rest/admin/files/${id}`, {
  body: JSON.stringify(data),
  method: 'PUT',
});

export const fetchAdminFileDetailsRest = id => adminFetch(`/rest/admin/files/${id}`);
