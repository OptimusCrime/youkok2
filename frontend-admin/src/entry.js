import "core-js/stable";
import "regenerator-runtime/runtime";
import 'whatwg-fetch';

import { run as diagnosticsRun } from "./diagnostics";
import { run as filesRun } from "./files";
import { run as filesPendingRun } from "./filesPending";
import { run as homeBoxesRun } from "./homeBoxes";
import { run as homeGraphRun } from "./homeGraph";

const bootstrap = () => {
  if (document.getElementById('admin-diagnostics-cache')) {
    diagnosticsRun();
  }
  if (document.getElementById('admin-files')) {
    filesRun();
  }
  if (document.getElementById('admin-files-pending')) {
    filesPendingRun();
  }
  if (document.getElementById('admin-home-boxes')) {
    homeBoxesRun();
  }
  if (document.getElementById('admin-home-graph')) {
    homeGraphRun();
  }

};

document.addEventListener("DOMContentLoaded", bootstrap);
