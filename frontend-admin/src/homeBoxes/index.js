import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import AdminHomeBoxes from './containers/main-container';
import configureStore from './redux/configureStore';
import {fetchAdminHomeBoxes} from "./redux/boxes/actions";

export const run = () => {
  const preloadedState = window.__INITIAL_STATE__;

  const store = configureStore(preloadedState);
  store.dispatch(fetchAdminHomeBoxes());

  ReactDOM.render((
      <Provider store={store}>
        <AdminHomeBoxes />
      </Provider>
    ), document.getElementById('admin-home-boxes')
  );
};
