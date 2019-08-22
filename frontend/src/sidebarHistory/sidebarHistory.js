import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SidebarMostPopularContainer from './containers/main-container';
import configureStore from './redux/configureStore';
import {fetchSidebarHistory} from "./redux/elements/actions";

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);
store.dispatch(fetchSidebarHistory());

ReactDOM.render((
    <Provider store={store}>
      <SidebarMostPopularContainer />
    </Provider>
  ), document.getElementById('sidebar-history')
);
