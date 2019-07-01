import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SidebarMostPopularContainer from './containers/main-container';
import configureStore from './redux/configureStore';
import {fetchSidebarMostPopular} from "./redux/elements/actions";

import './sidebarPopular.less';

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);
store.dispatch(fetchSidebarMostPopular());

ReactDOM.render((
    <Provider store={store}>
      <SidebarMostPopularContainer />
    </Provider>
  ), document.getElementById('sidebar-popular')
);