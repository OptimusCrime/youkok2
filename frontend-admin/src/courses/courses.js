import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import CourseMain from './containers/course-main';
import configureStore from './redux/configureStore';
import {getSearchFromUrl, queryPresentInUrl} from "./prefill";
import {COURSES_UPDATE_SEARCH} from "./redux/courses/constants";

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);

ReactDOM.render((
    <Provider store={store}>
      <CourseMain />
    </Provider>
  ), document.getElementById('courses')
);

// Check if we have search parameters from the URI
if (queryPresentInUrl()) {
  const searchValue = getSearchFromUrl();

  if (searchValue !== null) {
    store.dispatch({ type: COURSES_UPDATE_SEARCH, value: searchValue });
  }
}
