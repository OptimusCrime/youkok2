import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SearchBarContainer from './containers/main-container';
import configureStore from './redux/configureStore';

import './searchBar.less';

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);
//store.dispatch(fetchSidebarMostPopular());


ReactDOM.render((
    <Provider store={store}>
      <SearchBarContainer />
    </Provider>
  ), document.getElementById('search-bar')
);