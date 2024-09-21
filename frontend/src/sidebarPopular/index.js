import React from 'react';
import ReactDOM from 'react-dom/client';
import { Provider } from 'react-redux';

import SidebarMostPopularContainer from './containers/main-container';
import configureStore from './redux/configureStore';
import {fetchSidebarMostPopular} from "./redux/elements/actions";

export const run = () => {
  const preloadedState = window.__INITIAL_STATE__;

  const store = configureStore(preloadedState);
  store.dispatch(fetchSidebarMostPopular());

  const root = ReactDOM.createRoot(document.getElementById('sidebar-popular'));
    root.render(
      <Provider store={store}>
        <SidebarMostPopularContainer />
      </Provider>
    );
};
