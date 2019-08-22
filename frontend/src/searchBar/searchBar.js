import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SearchBarContainer from './containers/main-container';
import configureStore from './redux/configureStore';
import {CLOSE_SEARCH_RESULTS} from "./redux/form/constants";

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);

ReactDOM.render((
    <Provider store={store}>
      <SearchBarContainer />
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
