import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import AdminFilesPendingMainContainer from './containers/main-container';
import configureStore from './redux/configureStore';
import {fetchAdminFiles} from "../common/redux/files/actions";
import {fetchAdminFilesPendingRest} from "./api";

export const run = () => {
  const preloadedState = window.__INITIAL_STATE__;

  const store = configureStore(preloadedState);
  store.dispatch(fetchAdminFiles(fetchAdminFilesPendingRest));

  ReactDOM.render((
      <Provider store={store}>
        <AdminFilesPendingMainContainer />
      </Provider>
    ), document.getElementById('admin-files-pending')
  );
};
