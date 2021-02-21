import { adminFetch } from "../common/fetch";

export const fetchAdminFilesRest = () => {
  const path = window.location.pathname.split('/');
  const id = path[path.length - 1];

  if (!/^[0-9]+$/.test(id)) {
    return adminFetch('/rest/admin/files');
  }

  return adminFetch(`/rest/admin/files/single/${id}`);
}
