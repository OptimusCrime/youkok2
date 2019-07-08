import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from "react-redux";

import configureStore from "./redux/configureStore";
import { fetchArchive } from "./redux/archive/actions";

import { MainComponent } from './components/main-component';
import {getLocalStorageKeyForCurrentUri, removeExpiredCache} from "./utilities";
import {getItem, keyExists} from "../common/local-storage";
import {initArchive} from "./init";

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);

removeExpiredCache();

initArchive(store);

ReactDOM.render((
    <Provider store={store}>
      <MainComponent />
    </Provider>
  ), document.getElementById('archive')
);
