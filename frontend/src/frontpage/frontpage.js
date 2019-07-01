import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import { MainContainer } from './containers/main-container';
import configureStore from './redux/configureStore';
import { fetchFrontPageBoxes } from './redux/boxes/actions';
import {fetchFrontPageLastDownloaded} from "./redux/last_downloaded/actions";
import {fetchFrontPageLastVisited} from "./redux/last_visited/actions";
import {fetchFrontPageNewest} from "./redux/newest/actions";

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);
store.dispatch(fetchFrontPageBoxes());
store.dispatch(fetchFrontPageLastDownloaded());
store.dispatch(fetchFrontPageLastVisited());
store.dispatch(fetchFrontPageNewest());

ReactDOM.render((
    <Provider store={store}>
      <MainContainer />
    </Provider>
  ), document.getElementById('frontpage')
);
