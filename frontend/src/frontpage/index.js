import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import { MainContainer } from './containers/main-container';
import configureStore from './redux/configureStore';

import { fetchFrontPageBoxes } from './redux/boxes/actions';
import {fetchFrontPagePopularElements} from "./redux/popular_elements/actions";
import {fetchFrontPagePopularCourses} from "./redux/popular_courses/actions";
import {fetchFrontPageNewest} from "./redux/newest/actions";
import {fetchFrontPageLastDownloaded} from "./redux/last_downloaded/actions";
import {fetchFrontPageLastVisited} from "./redux/last_visited/actions";

export const run = () => {
  const preloadedState = window.__INITIAL_STATE__;

  const store = configureStore(preloadedState);
  store.dispatch(fetchFrontPageBoxes());
  store.dispatch(fetchFrontPagePopularElements());
  store.dispatch(fetchFrontPagePopularCourses());
  store.dispatch(fetchFrontPageNewest());
  store.dispatch(fetchFrontPageLastVisited());
  store.dispatch(fetchFrontPageLastDownloaded());

  ReactDOM.render((
      <Provider store={store}>
        <MainContainer />
      </Provider>
    ), document.getElementById('frontpage')
  );
}
