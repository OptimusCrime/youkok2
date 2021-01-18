import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import AdminHomeGraph from './containers/main-container';
import configureStore from './redux/configureStore';
import {fetchAdminHomeGraph} from "./redux/graph/actions";

export const run = () => {
  const preloadedState = window.__INITIAL_STATE__;

  const store = configureStore(preloadedState);
  store.dispatch(fetchAdminHomeGraph());

  ReactDOM.render((
      <Provider store={store}>
        <AdminHomeGraph />
      </Provider>
    ), document.getElementById('admin-home-graph')
  );
};
