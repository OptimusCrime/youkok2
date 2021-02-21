import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import configureStore from './redux/configureStore';
import {CLOSE_SEARCH_RESULTS} from "./redux/form/constants";
import LoaderWrapperContainer from "./containers/loader-wrapper";
import { SET_MODE } from "./redux/config/constants";
import {MODE_ADMIN} from "./constants";
import {refreshCourses} from "../common/coursesLookup";

// This entrypoint is written this way to allow us to directly copy it into
// the admin directory without modifications. This was done because I am lazy
// and because I could not figure out why webpack failed trying to build this
export const run = mode => {
  const preloadedState = window.__INITIAL_STATE__;

  const store = configureStore(preloadedState);

  store.dispatch({ type: SET_MODE, mode });

  if (mode === MODE_ADMIN) {
    // For admin users, just fetch the entire data set every single time
    refreshCourses();
  }

  ReactDOM.render((
      <Provider store={store}>
        <LoaderWrapperContainer />
      </Provider>
    ), document.getElementById('search-bar')
  );

// Handle click outside dropdown list, to close it
  document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener('click', (evt) => {
      const searchDropdown = document.querySelector('.search-bar__dropdown');

      // If the dropdown does not exist, exit early
      if (searchDropdown === null) {
        return;
      }

      let targetElement = evt.target;

      do {
        if (targetElement === searchDropdown) {
          // Click was inside the dropdown
          return;
        }

        // Traverse DOM tree
        targetElement = targetElement.parentNode;
      } while (targetElement);

      // Click was outside the dropdown, reset search
      store.dispatch({ type: CLOSE_SEARCH_RESULTS });
    });
  });
}

