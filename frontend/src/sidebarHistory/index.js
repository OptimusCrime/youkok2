import React from 'react';
import ReactDOM from 'react-dom/client';
import {Provider} from 'react-redux';

import SidebarMostPopularContainer from './containers/main-container';

export const run = store => {
    const root = ReactDOM.createRoot(document.getElementById('sidebar-history'));
    root.render(
        <Provider store={store}>
            <SidebarMostPopularContainer/>
        </Provider>
    );
};
