import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import CourseMain from './containers/course-main';
import configureStore from './redux/configureStore';

import './courses.less';

const preloadedState = window.__INITIAL_STATE__;

const store = configureStore(preloadedState);

ReactDOM.render((
    <Provider store={store}>
      <CourseMain />
    </Provider>
  ), document.getElementById('courses')
);
