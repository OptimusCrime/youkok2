export const postAdminCreateDirectoryRest = (directory, course, value) => fetch('/rest/admin/files/directory', {
  body: JSON.stringify({
    directory,
    course,
    value
  }),
  method: 'PUT',
});
