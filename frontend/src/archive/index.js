import React from 'react';
import ReactDOM from 'react-dom/client';
import {Provider} from "react-redux";

import configureStore from "./redux/configureStore";

import MainContainer from './containers/main-container';
import {fetchArchiveData} from "./redux/archive/actions";
import {splitUrlPath} from "./utilities";

import {run as runSidebarHistory} from "../sidebarHistory";
import {run as runSidebarPost} from "../sidebarPost";

export const run = () => {
    const preloadedState = window.__INITIAL_STATE__;

    const store = configureStore(preloadedState);

    const path = splitUrlPath();
    store.dispatch(fetchArchiveData(path));

    // Init the related sidebars with a shared store instance
    runSidebarHistory(store);
    runSidebarPost(store);

    const root = ReactDOM.createRoot(document.getElementById('archive'));
    root.render(
        <Provider store={store}>
            <MainContainer/>
        </Provider>
    );
};
