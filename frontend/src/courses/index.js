import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import LoaderWrapperContainer from './containers/loader-wrapper/loader-wrapper';
import configureStore from './redux/configureStore';
import {getSearchFromUrl, queryPresentInUrl} from "./prefill";
import {COURSES_UPDATE_SEARCH} from "./redux/courses/constants";

export const run = () => {
  const preloadedState = window.__INITIAL_STATE__;

  const store = configureStore(preloadedState);

  ReactDOM.render((
      <Provider store={store}>
        <LoaderWrapperContainer />
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
};
