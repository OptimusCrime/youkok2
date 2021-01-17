import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import MainContainer from './containers/main-container';

export const run = store => {
  ReactDOM.render((
      <Provider store={store}>
        <MainContainer />
      </Provider>
    ), document.getElementById('sidebar-post')
  );
};
