import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SidebarMostPopularContainer from './containers/main-container';

export const run = store => {
  ReactDOM.render((
      <Provider store={store}>
        <SidebarMostPopularContainer />
      </Provider>
    ), document.getElementById('sidebar-history')
  );
};
