import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import AdminDiagnosticsCache from './containers/main-container';
import configureStore from './redux/configureStore';
import {fetchAdminDiagnosticsCache} from "./redux/graph/actions";

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);
store.dispatch(fetchAdminDiagnosticsCache());

ReactDOM.render((
    <Provider store={store}>
      <AdminDiagnosticsCache />
    </Provider>
  ), document.getElementById('admin-diagnostics-cache')
);
