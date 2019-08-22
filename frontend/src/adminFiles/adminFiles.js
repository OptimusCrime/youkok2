import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import AdminFilesMainContainer from './containers/main-container';
import configureStore from './redux/configureStore';
import {fetchAdminFiles} from "../adminCommon/redux/files/actions";
import {fetchAdminFilesRest} from "./api";

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);
store.dispatch(fetchAdminFiles(fetchAdminFilesRest));

ReactDOM.render((
    <Provider store={store}>
      <AdminFilesMainContainer />
    </Provider>
  ), document.getElementById('admin-files')
);
