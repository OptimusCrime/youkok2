import React from 'react';
import ReactDOM from 'react-dom';

import { fetchAdminPendingNumRest } from "./api";

export const run = () => {
  // This is lazy
  fetchAdminPendingNumRest()
    .then(response => response.json())
    .then(response => {
      ReactDOM.render((
        `${response.num}`
        ), document.getElementById('admin-pending-num')
      );
    })
};
