import { adminFetch } from "../common/fetch";

export const fetchAdminPendingNumRest = () => adminFetch('/rest/admin/pending/num');
