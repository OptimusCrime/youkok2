import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from "react-redux";

import configureStore from "./redux/configureStore";
import { fetchArchive } from "./redux/archive/actions";

import MainContainer from './containers/main-container';

import './archive.less';


const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);
store.dispatch(fetchArchive());

ReactDOM.render((
    <Provider store={store}>
      <MainContainer />
    </Provider>
  ), document.getElementById('archive')
);