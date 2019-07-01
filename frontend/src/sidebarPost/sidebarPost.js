import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import { MainPostContainer } from './containers/main-container';
import configureStore from './redux/configureStore';

import './sidebarPost.less';

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);

ReactDOM.render((
    <Provider store={store}>
      <MainPostContainer />
    </Provider>
  ), document.getElementById('sidebar-post')
);
