import { adminFetch } from "../common/fetch";

export const fetchAdminFilesPendingRest = () => adminFetch('/rest/admin/files/pending');
