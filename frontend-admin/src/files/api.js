export const fetchAdminFilesRest = () => {
  if (!window.SITE_DATA.admin_file) {
    return fetch('/rest/admin/files');
  }

  return fetch(`/rest/admin/files/single/${window.SITE_DATA.admin_file}`);
}
