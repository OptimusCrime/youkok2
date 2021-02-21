import { adminFetch } from "../common/fetch";

export const fetchAdminDiagnosticsCacheRest = () => adminFetch('/rest/admin/diagnostics/cache');
