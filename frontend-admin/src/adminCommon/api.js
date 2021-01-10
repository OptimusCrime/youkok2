export const postAdminCreateDirectoryRest = (directory, course, value) => fetch('/rest/admin/files/directory', {
  body: JSON.stringify({
    directory,
    course,
    value
  }),
  method: 'PUT',
});

export const putAdminEditFileRest = (id, data) => fetch(`/rest/admin/files/${id}`, {
  body: JSON.stringify(data),
  method: 'PUT',
});

export const fetchAdminFileDetailsRest = id => fetch(`/rest/admin/files/${id}`);
