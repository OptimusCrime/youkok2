import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import MainContainer from './containers/main-container';
import configureStore from './redux/configureStore';

import './frontpage.less';

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);

ReactDOM.render((
    <Provider store={store}>
      <MainContainer />
    </Provider>
  ), document.getElementById('frontpage')
);