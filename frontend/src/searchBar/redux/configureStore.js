import { createStore, applyMiddleware, compose } from 'redux';
import { thunk } from 'redux-thunk';

import rootReducer from './rootReducer';

export default function configureStore(preloadedState) {
  const middleware = [
    thunk,
  ];

  return createStore(
    rootReducer,
    preloadedState,
    compose(
      applyMiddleware(...middleware),
      (process.env.NODE_ENV === 'development' && window.__REDUX_DEVTOOLS_EXTENSION__) ? window.__REDUX_DEVTOOLS_EXTENSION__() : f => f,
    )
  );
}
