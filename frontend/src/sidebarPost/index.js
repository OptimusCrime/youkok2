import React from 'react';
import ReactDOM from 'react-dom/client';
import { Provider } from 'react-redux';

import MainContainer from './containers/main-container';

export const run = store => {
    const root = ReactDOM.createRoot(document.getElementById('sidebar-post'));

    root.render(
      <Provider store={store}>
        <MainContainer />
      </Provider>
    );
};
