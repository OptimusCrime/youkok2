import "./admin/admin.less";

import { run as diagnostics } from "./diagnostics";
import { run as files } from "./files";
import { run as filesPending } from "./filesPending";
import { run as homeBoxes } from "./homeBoxes";
import { run as homeGraph } from "./homeGraph";
import { run as pendingNum } from "./pendingNum";
import { run as searchBar } from "./searchBar";
import {MODE_ADMIN} from "./searchBar/constants";

const bootstrap = () => {
  if (document.getElementById('admin-diagnostics-cache')) {
    diagnostics();
  }
  if (document.getElementById('admin-files')) {
    files();
  }
  if (document.getElementById('admin-files-pending')) {
    filesPending();
  }
  if (document.getElementById('admin-home-boxes')) {
    homeBoxes();
  }
  if (document.getElementById('admin-home-graph')) {
    homeGraph();
  }
  if (document.getElementById('admin-pending-num')) {
    pendingNum();
  }
  if (document.getElementById('search-bar')) {
    searchBar(MODE_ADMIN);
  }
};

document.addEventListener("DOMContentLoaded", bootstrap);
